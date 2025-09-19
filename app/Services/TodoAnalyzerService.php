<?php

namespace App\Services;

use App\Models\Todo;
use App\Enums\SenderType;
use App\Enums\TodoStatus;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TodoAnalyzerService
{
    public function __construct(private GeminiApiService $geminiApi)
    {
    }

    public function continueConsultation(Todo $todo, string $userMessage): array
    {
        return DB::transaction(function () use ($todo, $userMessage) {
            // 1. Simpan pesan dari pengguna
            $todo->aiConsultationMessages()->create([
                'user_id' => auth()->id(),
                'sender_type' => SenderType::User,
                'message_text' => $userMessage,
            ]);

            // 2. Siapkan data untuk dikirim ke Gemini
            $history = $todo->aiConsultationMessages()->orderBy('created_at')->get();
            $systemPrompt = $this->buildSystemPrompt($todo);
            $tools = $this->defineTools();

            // 3. Panggil Gemini API
            $responseArray = $this->geminiApi->generateContent($systemPrompt, $history, $tools);
            
            if (isset($responseArray['functionCall'])) {
                $aiReplyText = $this->handleFunctionCall($todo, $responseArray['functionCall']);
            } elseif (isset($responseArray['subtasks']) || isset($responseArray['sub_tugas'])) {
                Log::debug('AI returned subtasks directly. Handling as function call.');
                $subtaskList = $responseArray['subtasks'] ?? $responseArray['sub_tugas'];
                $functionCall = [
                    'name' => 'create_subtasks',
                    'args' => ['subtasks' => $subtaskList]
                ];
                $aiReplyText = $this->handleFunctionCall($todo, $functionCall);
            } else {
                $aiReplyText = $responseArray['text'] ?? 'Maaf, saya tidak bisa memproses permintaan itu saat ini.';
            }
            
            // 5. Simpan balasan dari AI
            $aiMessage = $todo->aiConsultationMessages()->create([
                'user_id' => auth()->id(),
                'sender_type' => SenderType::Ai,
                'message_text' => trim($aiReplyText),
            ]);

            Log::info('Transaction for AI consultation is about to commit.'); // Log konfirmasi

            return [
                'reply' => $aiMessage,
                'todo' => $todo->fresh(['subtasks']),
            ];
        });
    }

    /**
     * [DIUBAH] Menambahkan logging agresif untuk debugging.
     */
    private function handleFunctionCall(Todo $parentTask, array $functionCall): string
    {
        $functionName = $functionCall['name'];
        $arguments = $functionCall['args'];
        
        if ($functionName === 'create_subtasks') {
            $subtaskTitles = $arguments['subtasks'] ?? [];
            if (empty($subtaskTitles)) {
                Log::warning('handleFunctionCall was called, but no subtask titles were found in arguments.', $arguments);
                return "Maaf, saya tidak menemukan judul sub-tugas untuk ditambahkan.";
            }

            Log::info('Attempting to create subtasks.', ['titles' => $subtaskTitles]);

            $createdCount = 0;
            foreach ($subtaskTitles as $title) {
                if (!is_string($title) || trim($title) === '') continue;
                
                try {
                    $subtask = $parentTask->subtasks()->create([
                        'user_id' => $parentTask->user_id,
                        'project_id' => $parentTask->project_id,
                        'title' => $title,
                        'status' => TodoStatus::Todo,
                    ]);
                    Log::info('Subtask created successfully in memory.', ['id' => $subtask->id, 'title' => $subtask->title]);
                    $createdCount++;
                } catch (Exception $e) {
                    Log::error('Failed to create a subtask inside loop.', [
                        'error' => $e->getMessage(),
                        'title' => $title
                    ]);
                    // Jangan hentikan loop, lanjutkan ke sub-tugas berikutnya
                }
            }

            if ($createdCount > 0) {
                return "Oke, saya sudah menambahkan {$createdCount} sub-tugas baru untuk Anda. Silakan periksa daftar sub-tugas Anda.";
            } else {
                return "Maaf, terjadi kesalahan saat mencoba menambahkan sub-tugas.";
            }
        }

        return "Maaf, saya tidak mengenali fungsi '{$functionName}'.";
    }

    private function defineTools(): array
    {
        return [
            [
                'functionDeclarations' => [
                    [
                        'name' => 'create_subtasks',
                        'description' => 'Membuat beberapa sub-tugas baru di bawah tugas utama yang sedang dibahas.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'subtasks' => [
                                    'type' => 'ARRAY',
                                    'description' => 'Daftar judul (string) untuk setiap sub-tugas yang akan dibuat.',
                                    'items' => ['type' => 'STRING']
                                ]
                            ],
                            'required' => ['subtasks']
                        ]
                    ]
                ]
            ]
        ];
    }
    
    private function buildSystemPrompt(Todo $todo): string
    {
        $taskDetails = "Judul Tugas: '{$todo->title}'.";
        if ($todo->notes) {
            $taskDetails .= " Catatan: '{$todo->notes}'.";
        }
        if ($todo->subtasks()->exists()) {
            $subtasks = $todo->subtasks->pluck('title')->implode(', ');
            $taskDetails .= " Sub-tugas yang sudah ada: [{$subtasks}].";
        }

        return "Anda adalah 'RelaxMate', asisten produktivitas ahli. " .
               "Tugas utama Anda adalah memecah tugas pengguna menjadi sub-tugas yang bisa ditindaklanjuti. " .
               "Ketika Anda sudah mengidentifikasi langkah-langkahnya, Anda HARUS SELALU dan LANGSUNG menggunakan fungsi `create_subtasks` untuk menambahkannya. " .
               "JANGAN pernah menyajikan daftar sub-tugas sebagai teks untuk dikonfirmasi pengguna. " .
               "Selalu panggil fungsi secara proaktif. " .
               "Konteks tugas saat ini adalah: {$taskDetails}";
    }
}