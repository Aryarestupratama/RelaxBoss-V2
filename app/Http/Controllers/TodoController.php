<?php

namespace App\Http\Controllers;

use App\Models\PomodoroSession; // Pastikan ini di-import
use App\Models\Project;
use App\Models\Todo;
use App\Services\AiOrchestratorService; // Import service AI
use Carbon\Carbon;
use Exception; // Import Exception
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TodoController extends Controller
{
    use AuthorizesRequests;

    protected AiOrchestratorService $aiOrchestratorService; // Deklarasikan properti service AI

    // Injeksi AiOrchestratorService melalui constructor
    public function __construct(AiOrchestratorService $aiOrchestratorService)
    {
        $this->aiOrchestratorService = $aiOrchestratorService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $activeProjectId = $request->input('project_id');
        $projects = $user->projects()->withCount('todos')->orderBy('name')->get();

        $tasksQuery = $user->todos()
            ->whereNull('parent_task_id')
            ->with(['project', 'files', 'subtasks', 'pomodoroSessions' => function ($query) {
                // Ambil hanya sesi terakhir yang statusnya dijeda
                $query->where('status', 'interrupted')->latest()->limit(1);
            }])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        if ($activeProjectId) {
            $tasksQuery->where('project_id', $activeProjectId);
        }

        $tasks = $tasksQuery->get()->groupBy('status');

        $groupedTasks = [
            'todo'          => $tasks->get('todo', collect()),
            'in_progress'   => $tasks->get('in_progress', collect()),
            'done'          => $tasks->get('done', collect()),
        ];

        // [PERUBAHAN KUNCI] Cek apakah request meminta JSON
        if ($request->expectsJson()) {
            return response()->json($groupedTasks);
        }

        return view('user.todos.index', [
            'tasks'           => $groupedTasks,
            'projects'        => $projects,
            'activeProjectId' => $activeProjectId,
        ]);
    }

    /**
     * Menyimpan tugas baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'task' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date_format:Y-m-d\TH:i',
            'priority' => 'required|in:low,medium,high,focus',
            'eisenhower_quadrant' => 'nullable|in:do,schedule,delegate,delete',
            'subtasks' => 'nullable|json',
            'file' => 'nullable|file|max:10240',
            'pomodoro_custom_duration' => 'nullable|integer|min:1',
        ]);

        $todo = Auth::user()->todos()->create($data);

        $this->syncSubtasks($todo, $request);

        if ($request->hasFile('file')) {
            $this->uploadFile($request->file('file'), $todo);
        }

        // Kembalikan todo yang baru dibuat dengan relasi yang mungkin dibutuhkan di frontend
        return response()->json([
            'message' => 'Tugas berhasil ditambahkan!',
            'task' => $todo->fresh()->load('project', 'files', 'subtasks') // [PERBAIKAN] Ganti 'todo' menjadi 'task'
        ], 201);
    }

    public function update(Request $request, Todo $todo): JsonResponse
    {
        // Otorisasi: Pastikan pengguna hanya bisa mengedit tugas miliknya
        $this->authorize('update', $todo);

        $data = $request->validate([
            'task' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date_format:Y-m-d\TH:i',
            'priority' => 'required|in:low,medium,high,focus',
            'eisenhower_quadrant' => 'nullable|in:do,schedule,delegate,delete',
            'subtasks' => 'nullable|json',
            'file' => 'nullable|file|max:10240',
            'pomodoro_custom_duration' => 'nullable|integer|min:1',
        ]);

        $todo->update($data);

        $this->syncSubtasks($todo, $request);

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada, lalu upload yang baru
            if ($todo->files->isNotEmpty()) {
                foreach ($todo->files as $file) {
                    Storage::disk('public')->delete($file->file_path);
                    $file->delete();
                }
            }
            $this->uploadFile($request->file('file'), $todo);
        }

        // [PERBAIKAN KUNCI] Pastikan baris return ini memuat ulang semua relasi
        // dengan nama yang benar ('subtasks' huruf kecil).
        return response()->json([
            'message' => 'Tugas berhasil diperbarui!',
            'task' => $todo->fresh()->load('project', 'files', 'subtasks') // Pastikan ini 'task'
        ]);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $this->authorize('delete', $todo);

        // Hapus file terkait dari storage sebelum menghapus record
        foreach ($todo->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $todo->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus.']);
    }

    public function updateStatus(Request $request, Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done'
        ]);

        $newStatus = $validated['status'];
        if ($newStatus === 'done' && $todo->parent_task_id === null) { // Hanya berlaku untuk tugas utama
            $pendingSubtasks = $todo->subtasks()->where('status', '!=', 'done')->exists();
            if ($pendingSubtasks) {
                return response()->json([
                    'message' => 'Harap selesaikan semua sub-tugas terlebih dahulu.',
                    'task' => $todo->fresh()->load('subtasks')
                ], 422);
            }
        }

        // Update status tugas utama
        $todo->status = $newStatus;
        $todo->completed_at = ($newStatus === 'done') ? now() : null;
        $todo->save();

        // Jika tugas utama selesai, selesaikan juga semua sub-tugasnya
        if ($newStatus === 'done') {
            $todo->subtasks()->update([
                'status' => 'done',
                'completed_at' => now()
            ]);
        }

        // [PERUBAHAN] Muat ulang data tugas beserta relasi sub-tugas yang sudah ter-update
        // dan kirim kembali sebagai respons JSON.
        return response()->json([
            'message' => 'Tugas berhasil diperbarui!',
            'task' => $todo->fresh()->load('project', 'files', 'subtasks') // Pastikan ini 'task'
        ]);
    }

    public function togglePin(Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        // [REKOMENDASI] Tambahkan pengecekan ini
        if ($todo->priority !== 'focus') {
            $focusLimit = 3; // Tentukan batas di sini
            $currentFocusCount = Auth::user()->todos()->where('priority', 'focus')->count();
            if ($currentFocusCount >= $focusLimit) {
                return response()->json([
                    'message' => "Anda hanya dapat memiliki maksimal {$focusLimit} tugas fokus dalam satu waktu.",
                    'priority' => $todo->priority
                ], 422); // Unprocessable Entity
            }
            if (in_array($todo->eisenhower_quadrant, ['delegate', 'delete'])) {
                return response()->json([
                    'message' => 'Tugas yang akan didelegasikan atau dihapus tidak dapat menjadi fokus utama.',
                    'priority' => $todo->priority // Kembalikan prioritas saat ini
                ], 409); // 409 Conflict
            }
        }

        $todo->priority = $todo->priority === 'focus' ? 'medium' : 'focus';
        $todo->save();

        return response()->json([
            'message' => 'Prioritas tugas diubah.',
            'priority' => $todo->priority
        ]);
    }

    private function uploadFile($file, $todo)
    {
        $path = $file->store('tasks/files', 'public');
        $mimeType = $file->getClientMimeType();

        // Logika untuk menentukan kategori berdasarkan Tipe MIME
        $category = match (true) {
            str_starts_with($mimeType, 'image/') => 'gambar',
            $mimeType === 'application/pdf' => 'dokumen',
            in_array($mimeType, [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]) => 'dokumen',
            str_starts_with($mimeType, 'video/') => 'video',
            str_starts_with($mimeType, 'audio/') => 'audio',
            default => 'lainnya',
        };

        $todo->files()->create([
            'file_category' => $category, // Simpan kategori yang terdeteksi
            'file_type' => $mimeType,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
        ]);
    }

    // --- METODE UNTUK MANAJEMEN PROYEK (TIDAK ADA PERUBAHAN) ---
    public function storeProject(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Auth::user()->projects()->create($request->all());

        // [PERBAIKAN] Muat jumlah tugas untuk proyek yang baru dibuat.
        $project->loadCount('todos');

        return response()->json([
            'message' => 'Proyek berhasil ditambahkan.',
            'project' => $project
        ], 201);
    }

    public function updateProject(Request $request, Project $project): JsonResponse
    {
        // Gunakan Policy atau Gate untuk otorisasi yang lebih rapi
        if ($project->user_id !== Auth::id()) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403); // 403 Forbidden
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($request->all());

        // Kembalikan respons JSON
        return response()->json([
            'message' => 'Proyek berhasil diperbarui.',
            'project' => $project->fresh() // Kirim data terbaru
        ]);
    }

    public function destroyProject(Project $project): JsonResponse
    {
        if ($project->user_id !== Auth::id()) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        // Transaksi database adalah ide bagus jika ada banyak operasi terkait
        $project->todos()->delete();
        $project->delete();

        // Kembalikan respons JSON
        return response()->json(['message' => 'Proyek berhasil dihapus.']);
    }
    // --- AKHIR METODE UNTUK MANAJEMEN PROYEK ---

    private function syncSubtasks(Todo $parentTask, Request $request)
    {
        // Decode string JSON dari request
        $subtasksData = json_decode($request->input('subtasks', '[]'), true);
        if (empty($subtasksData)) {
            // Jika tidak ada sub-tugas, hapus semua yang lama dan selesai
            $parentTask->subtasks()->delete(); // Perbaiki 'subTasks' menjadi 'subtasks'
            return;
        }

        $submittedIds = [];

        foreach ($subtasksData as $subtaskItem) {
            $subtaskData = [
                'task' => $subtaskItem['task'],
                'status' => $subtaskItem['status'] ?? 'todo', // Default status
                'user_id' => $parentTask->user_id, // Pastikan user_id sama
            ];

            if (!empty($subtaskItem['id'])) {
                // Jika ada ID, ini adalah sub-tugas yang sudah ada -> UPDATE
                $subtask = Todo::find($subtaskItem['id']);
                if ($subtask) {
                    $subtask->update($subtaskData);
                    $submittedIds[] = $subtask->id;
                }
            } else {
                // Jika tidak ada ID, ini adalah sub-tugas baru -> CREATE
                $newSubtask = $parentTask->subtasks()->create($subtaskData); // Perbaiki 'subTasks' menjadi 'subtasks'
                $submittedIds[] = $newSubtask->id;
            }
        }

        // Hapus sub-tugas lama yang tidak ada dalam daftar yang dikirim
        $parentTask->subtasks()->whereNotIn('id', $submittedIds)->delete(); // Perbaiki 'subTasks' menjadi 'subtasks'
        $allSubtasksDone = !$parentTask->subtasks()->where('status', '!=', 'done')->exists();
        $hasSubtasks = $parentTask->subtasks()->count() > 0;

        if ($hasSubtasks && $allSubtasksDone && $parentTask->status !== 'done') {
            $parentTask->status = 'done';
            $parentTask->completed_at = now();
            $parentTask->save();
        }
    }

    public function updateQuadrant(Request $request, Todo $todo)
    {
        $this->authorize('update', $todo);

        $validated = $request->validate([
            'quadrant' => ['required', 'string', \Illuminate\Validation\Rule::in(['do', 'schedule', 'delegate', 'delete'])],
        ]);

        // [REKOMENDASI] Tambahkan pengecekan ini
        if (in_array($validated['quadrant'], ['delegate', 'delete'])) {
            if ($todo->priority === 'focus') {
                return response()->json([
                    'message' => 'Tugas yang sedang menjadi "Fokus Hari Ini" tidak dapat didelegasikan atau dihapus.',
                    'task' => $todo->fresh()->load('project', 'files', 'subtasks')
                ], 409); // 409 Conflict
            }
        }

        $todo->update([
            'eisenhower_quadrant' => $validated['quadrant'],
        ]);

        return response()->json([
            'message' => 'Kuadran tugas berhasil diperbarui!', // Perbaiki pesan
            'task' => $todo->fresh()->load('project', 'files', 'subtasks')
        ]);
    }

    /**
     * Menganalisis teks tugas menggunakan AI.
     * Endpoint ini akan dipanggil secara terpisah untuk mendapatkan saran AI.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeTaskWithAi(Request $request): JsonResponse
    {
        $request->validate([
            'task_text' => 'required|string|max:1000',
            'history' => 'array' // Opsional, untuk riwayat percakapan jika mode chat
        ]);

        $userInput = $request->input('task_text');
        $history = $request->input('history', []);

        try {
            // Panggil AiOrchestratorService
            $aiResponseFromService = $this->aiOrchestratorService->handle(
                'todo_assistant_analyzer',
                $userInput,
                $history
            );

            // Log respons lengkap dari service untuk debugging
            Log::info("AI Orchestrator Service Response: " . json_encode($aiResponseFromService));

            // Ambil ai_parsed_response dari array yang dikembalikan oleh service
            $aiParsedResponse = $aiResponseFromService['ai_parsed_response'] ?? '';

            return response()->json([
                'success' => true,
                'message' => 'Analisis AI berhasil.',
                'ai_raw_response' => $aiResponseFromService['ai_raw_response_from_gemini'], // Respons mentah dari Gemini
                'ai_parsed_response' => $aiParsedResponse, // Teks JSON yang sudah dibersihkan
            ]);

        } catch (Exception $e) {
            Log::error("Error during AI task analysis: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menganalisis tugas dengan AI: ' . $e->getMessage(),
            ], 500);
        }
    }
}