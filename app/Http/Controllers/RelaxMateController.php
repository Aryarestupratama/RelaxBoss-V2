<?php

// app/Http/Controllers/RelaxMateController.php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\RelaxMateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RelaxMateController extends Controller
{
    protected RelaxMateService $relaxMateService;

    public function __construct(RelaxMateService $relaxMateService)
    {
        $this->relaxMateService = $relaxMateService;
    }

    /**
     * Menampilkan halaman utama antarmuka chat AI RelaxMate.
     */
    public function index()
    {
        return view('user.relaxmate.index');
    }

    /**
     * [DIUBAH TOTAL] Logika dirombak agar lebih andal dan efisien.
     */
    public function getConversationGroups()
    {
        try {
            $user = Auth::user();
            
            // 1. Ambil semua ID percakapan unik, diurutkan dari yang terbaru
            $conversationIds = ChatMessage::where('user_id', $user->id)
                ->latest()
                ->pluck('conversation_id')
                ->unique();

            if ($conversationIds->isEmpty()) {
                return response()->json([]);
            }

            // 2. Ambil semua pesan yang relevan dalam satu query untuk efisiensi
            $allMessages = ChatMessage::where('user_id', $user->id)
                ->whereIn('conversation_id', $conversationIds)
                ->orderBy('created_at', 'asc')
                ->get()
                ->groupBy('conversation_id');

            // 3. Proses data menggunakan Koleksi Laravel yang aman
            $formatted = $conversationIds->map(function ($convId) use ($allMessages) {
                $messagesInConv = $allMessages->get($convId);

                if (!$messagesInConv) {
                    return null;
                }

                // Cari judul yang dibuat oleh AI
                $aiTitle = $messagesInConv->first(function ($msg) {
                    return $msg->sender_type === 'ai' && isset($msg->metadata['is_title']) && $msg->metadata['is_title'] === true;
                });

                // Cari pesan pertama dari pengguna sebagai fallback judul
                $firstUserMessage = $messagesInConv->firstWhere('sender_type', 'user');

                $title = 'Percakapan Baru'; // Judul default jika tidak ada pesan user
                if ($aiTitle) {
                    $title = $aiTitle->message_text;
                } elseif ($firstUserMessage) {
                    $title = Str::limit($firstUserMessage->message_text, 35);
                }
                
                return [
                    'conversation_id' => $convId,
                    'title' => $title,
                    'created_at' => $messagesInConv->first()->created_at, // Untuk pengelompokan berdasarkan hari
                    'latest_activity' => $messagesInConv->last()->created_at // Untuk pengurutan
                ];
            })->filter()->sortByDesc('latest_activity');

            // 4. Kelompokkan berdasarkan tanggal untuk ditampilkan di sidebar
            $grouped = $formatted->groupBy(function ($item) {
                return $item['created_at']->format('Y-m-d');
            });

            return response()->json($grouped);

        } catch (\Exception $e) {
            Log::error('CRASH di RelaxMateController@getConversationGroups', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Gagal memuat grup percakapan.'], 500);
        }
    }

    /**
     * [BARU] Mengambil percakapan spesifik hari ini.
     */
    public function getTodaysConversation()
    {
        $todaysConversation = ChatMessage::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        if (!$todaysConversation) {
            return response()->json(['conversation_id' => null, 'messages' => []]);
        }

        $messages = ChatMessage::where('user_id', Auth::id())
            ->where('conversation_id', $todaysConversation->conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'conversation_id' => $todaysConversation->conversation_id,
            'messages' => $messages,
        ]);
    }

    /**
     * Mengambil percakapan terakhir yang diakses pengguna.
     */
    public function getLatestConversation()
    {
        $latestConversation = ChatMessage::where('user_id', Auth::id())
            ->latest()
            ->first();

        if (!$latestConversation) {
            // [PERBAIKAN] Pastikan untuk mengembalikan ID null jika tidak ada percakapan
            return response()->json(['conversation_id' => null, 'messages' => []]);
        }

        $messages = ChatMessage::where('user_id', Auth::id())
            ->where('conversation_id', $latestConversation->conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'conversation_id' => $latestConversation->conversation_id,
            'messages' => $messages,
        ]);
    }

    /**
     * Mengambil riwayat pesan untuk percakapan tertentu.
     */
    public function getHistory(Request $request)
    {
        try {
            if (!Auth::check()) {
                Log::warning('RelaxMate getHistory: Percobaan akses tanpa autentikasi.');
                return response()->json(['error' => 'Sesi Anda tidak valid. Silakan muat ulang halaman.'], 401);
            }

            $validated = $request->validate([
                'conversation_id' => 'required|string|max:255',
            ]);

            $userId = Auth::id();
            $messages = $this->relaxMateService->getConversationHistory($userId, $validated['conversation_id']);

            return response()->json($messages);

        } catch (ValidationException $e) {
            return response()->json(['error' => 'Conversation ID tidak valid.'], 422);
        } catch (\Exception $e) {
            Log::error('CRASH di RelaxMateController@getHistory', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan internal saat mengambil riwayat.'], 500);
        }
    }

    /**
     * Mengirim pesan baru ke AI dan mendapatkan balasan.
     */
    public function sendMessage(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Sesi Anda tidak valid. Silakan muat ulang halaman.'], 401);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $userMessage = $validated['message'];
        $conversationId = $validated['conversation_id'];
    
        try {
            $user = Auth::user();
            $userMessage = $request->input('message');
            $conversationId = $request->input('conversation_id');
            
            $this->relaxMateService->saveMessage($user->id, $conversationId, 'user', $userMessage);
            
            $aiResponse = $this->relaxMateService->getAiReply($user, $conversationId);

            $aiMessage = null;
            if (!empty($aiResponse['reply'])) {
                $aiMessage = $this->relaxMateService->saveMessage(
                    $user->id, $conversationId, 'ai', $aiResponse['reply'], $aiResponse['metadata'] ?? null
                );
            }

            $messageCount = ChatMessage::where('conversation_id', $conversationId)->count();
            if ($messageCount >= 4 && $messageCount % 2 == 0) { // Cek setelah 4, 6, 8, dst. pesan
                $this->relaxMateService->generateAndSaveTitle($conversationId);
            }

            if (!$aiMessage) {
                return response()->json(['error' => 'AI tidak dapat memberikan balasan saat ini.'], 503);
            }
            
            return response()->json(['reply' => $aiMessage]);

        } catch (\Exception $e) {
            Log::error('CRASH di RelaxMateController@sendMessage', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Maaf, terjadi kesalahan di server kami.'], 500);
        }
    }

    /**
     * Menghapus riwayat percakapan pengguna.
     */
    public function clearConversation(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Sesi Anda tidak valid.'], 401);
        }

        $validated = $request->validate([
            'conversation_id' => 'required|string|max:255',
        ]);

        try {
            // Anda perlu membuat metode ini di dalam RelaxMateService Anda
            $this->relaxMateService->deleteConversationHistory(Auth::id(), $validated['conversation_id']);
            
            return response()->json(['success' => 'Riwayat percakapan berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('CRASH di RelaxMateController@clearConversation', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal menghapus percakapan.'], 500);
        }
    }
}

