<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\PomodoroSession;
use App\Services\TodoAnalyzerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

// Import semua Enum yang relevan
use App\Enums\TodoStatus;
use App\Enums\TodoPriority;
use App\Enums\EisenhowerQuadrant;
use App\Enums\PomodoroType;
use App\Enums\SessionStatus;

class TodoController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TodoAnalyzerService $todoAnalyzerService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * [DIUBAH] Metode index sekarang mengembalikan koleksi datar (flat collection)
     * dan controller lama untuk menangani permintaan view.
     */
    public function index(Request $request)
    {
        $projects = Auth::user()->projects()->withCount('todos')->orderBy('name')->get();

        $tasks = Auth::user()->todos()
            ->whereNull('parent_task_id')
            ->with(['project'])
            ->when($request->filled('project_id'), function ($query) use ($request) {
                return $query->where('project_id', $request->project_id);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // [BARU] Ambil sesi yang dijeda untuk ditampilkan di UI
        $interruptedSession = Auth::user()->pomodoroSessions()
            ->where('status', SessionStatus::Interrupted)
            ->latest('updated_at')
            ->first();
        
        // Untuk permintaan API, kita bisa tetap mengirimkan data datar atau mengelompokkannya di sini
        if ($request->expectsJson()) {
            return response()->json($tasks);
        }

        // Untuk view, kirim semua data yang diperlukan
        return view('user.todos.index', [
            'tasks'           => $tasks, // Kirim sebagai koleksi datar
            'projects'        => $projects,
            'activeProjectId' => $request->input('project_id'),
            'interruptedSession' => $interruptedSession,
        ]);
    }

    /**
     * [BARU] Metode untuk memperbarui quadrant Eisenhower.
     */
    public function updateQuadrant(Request $request, Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $validated = $request->validate([
            'quadrant' => ['required', Rule::enum(EisenhowerQuadrant::class)],
        ]);

        $todo->eisenhower_quadrant = $validated['quadrant'];
        $todo->save();

        return response()->json($todo->fresh());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|integer|exists:projects,id',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => ['required', Rule::enum(TodoPriority::class)],
            'eisenhower_quadrant' => ['nullable', Rule::enum(EisenhowerQuadrant::class)],
            'pomodoro_custom_duration' => 'nullable|integer|min:1', // <-- [DIUBAH]
            'subtasks' => 'nullable|array',
            'subtasks.*.title' => 'required_with:subtasks|string|max:255',
        ]);

        if (isset($validated['project_id'])) {
            Auth::user()->projects()->findOrFail($validated['project_id']);
        }

        $todo = Auth::user()->todos()->create($validated);

        if (!empty($validated['subtasks'])) {
            $this->syncSubtasks($todo, $validated['subtasks']);
        }

        return response()->json($todo->load('project', 'files', 'subtasks'), 201);
    }
    
    public function show(Todo $todo): JsonResponse
    {
        $this->authorize('view', $todo);
        return response()->json($todo->load('project', 'files', 'subtasks', 'pomodoroSessions'));
    }

    public function update(Request $request, Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'project_id' => 'nullable|integer|exists:projects,id',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => ['sometimes', 'required', Rule::enum(TodoPriority::class)],
            'eisenhower_quadrant' => ['nullable', Rule::enum(EisenhowerQuadrant::class)],
            'pomodoro_custom_duration' => 'nullable|integer|min:1', // <-- [DIUBAH]
            'subtasks' => 'nullable|array',
            'subtasks.*.id' => 'nullable|integer|exists:todos,id',
            'subtasks.*.title' => 'required_with:subtasks|string|max:255',
        ]);

        $todo->update($validated);

        if ($request->has('subtasks')) {
            $this->syncSubtasks($todo, $validated['subtasks']);
        }

        return response()->json($todo->fresh()->load('project', 'files', 'subtasks'));
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $this->authorize('delete', $todo);
        $todo->delete();
        return response()->json(['message' => 'Tugas berhasil dihapus.']);
    }

    public function updateStatus(Request $request, Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(TodoStatus::class)],
        ]);
        
        $newStatus = TodoStatus::from($validated['status']);

        if ($newStatus === TodoStatus::Done && $todo->subtasks()->whereNot('status', TodoStatus::Done)->exists()) {
            return response()->json(['message' => 'Selesaikan semua sub-tugas terlebih dahulu.'], 422);
        }

        $todo->status = $newStatus;
        $todo->completed_at = ($newStatus === TodoStatus::Done) ? now() : null;
        $todo->save();

        return response()->json($todo->fresh());
    }

    /**
     * [DIUBAH] Memulai sesi Pomodoro dengan validasi sub-tugas.
     */
    public function startPomodoro(Request $request, Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        if (Auth::user()->pomodoroSessions()->where('status', SessionStatus::Running)->exists()) {
             return response()->json(['message' => 'Anda sudah memiliki sesi Pomodoro yang sedang berjalan.'], 409);
        }

        // [BARU] Perbarui status tugas menjadi 'in_progress' jika masih 'todo'
        if ($todo->status === TodoStatus::Todo) {
            $todo->status = TodoStatus::InProgress;
            $todo->save();
        }

        // [BARU] Logika untuk menyelesaikan sub-tugas jika diminta
        if ($request->input('force_complete_subtasks', false)) {
            $todo->subtasks()->update([
                'status' => TodoStatus::Done,
                'completed_at' => now(),
            ]);
        }

        $duration = $todo->pomodoro_custom_duration ?? 25;

        $workSession = $todo->pomodoroSessions()->create([
            'user_id' => Auth::id(),
            'type' => PomodoroType::Work,
            'status' => SessionStatus::Running,
            'duration_minutes' => $duration,
            'start_time' => now()
        ]);
        
        $sessions = collect([$workSession]);

        // [DIUBAH] Logika istirahat sekarang mengecek durasi > 5 menit
        if ($duration > 5) {
            $breakDuration = round($duration / 5);
            if ($breakDuration > 0) {
                $breakSession = $todo->pomodoroSessions()->create([
                    'user_id' => Auth::id(),
                    'type' => PomodoroType::ShortBreak,
                    'status' => SessionStatus::Scheduled,
                    'duration_minutes' => $breakDuration,
                ]);
                $sessions->push($breakSession);
            }
        }

        return response()->json([
            'message' => 'Sesi Pomodoro dimulai!',
            'sessions' => $sessions,
            'updated_todo_status' => $todo->status->value
        ], 201);
    }

    /**
     * [BARU] Metode untuk melanjutkan sesi Pomodoro yang dijeda.
     */
    public function resumePomodoro(PomodoroSession $session): JsonResponse
    {
        $this->authorize('update', $session->todo);

        // Pastikan sesi ini memang dijeda
        if ($session->status !== SessionStatus::Interrupted) {
            return response()->json(['message' => 'Sesi ini tidak sedang dijeda.'], 409);
        }

        // Pastikan tidak ada sesi lain yang sedang berjalan
        if (Auth::user()->pomodoroSessions()->where('status', SessionStatus::Running)->exists()) {
            return response()->json(['message' => 'Anda sudah memiliki sesi lain yang sedang berjalan.'], 409);
        }

        $session->status = SessionStatus::Running;
        $session->save();

        return response()->json($session->fresh());
    }
    
    public function updatePomodoro(Request $request, PomodoroSession $session): JsonResponse
    {
        $this->authorize('update', $session->todo);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(SessionStatus::class)],
            'remaining_seconds' => 'nullable|integer|min:0',
        ]);
        
        $newStatus = SessionStatus::from($validated['status']);
        
        $session->status = $newStatus;
        if($newStatus === SessionStatus::Completed) {
            $session->end_time = now();
            $session->todo->increment('pomodoro_cycles_completed');
        }
        if($request->has('remaining_seconds')) {
            $session->remaining_seconds = $validated['remaining_seconds'];
        }
        $session->save();
        
        return response()->json($session->fresh());
    }

    /**
     * [AKTIFKAN] Mengambil riwayat konsultasi AI untuk sebuah tugas.
     */
    public function getAiMessages(Todo $todo): JsonResponse
    {
        $this->authorize('view', $todo);
        $messages = $todo->aiConsultationMessages()->orderBy('created_at')->get();
        return response()->json($messages);
    }

    /**
     * [AKTIFKAN] Mengirim pesan baru ke sesi konsultasi AI.
     */
    public function askAi(Request $request, Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $validated = $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        try {
            // Memanggil service yang berisi logika untuk berinteraksi dengan AI
            $result = $this->todoAnalyzerService->continueConsultation($todo, $validated['message']);

            return response()->json([
                'reply' => $result['reply'], // Balasan dari AI
            ], 201);

        } catch (Exception $e) {
            Log::error("Gagal melakukan konsultasi AI: " . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat berkomunikasi dengan AI.'], 500);
        }
    }

    private function syncSubtasks(Todo $parentTask, array $subtasksData): void
    {
        $submittedIds = [];

        foreach ($subtasksData as $subtaskItem) {
            $subtask = $parentTask->subtasks()->updateOrCreate(
                [
                    'id' => $subtaskItem['id'] ?? null,
                ],
                [
                    'title' => $subtaskItem['title'],
                    'user_id' => $parentTask->user_id,
                    'status' => isset($subtaskItem['status']) ? TodoStatus::from($subtaskItem['status']) : TodoStatus::Todo,
                ]
            );
            $submittedIds[] = $subtask->id;
        }

        $parentTask->subtasks()->whereNotIn('id', $submittedIds)->delete();
    }
}