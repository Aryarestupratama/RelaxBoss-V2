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
     * Mengambil daftar percakapan, dikelompokkan berdasarkan tanggal.
     */
    public function getConversationGroups()
    {
        try {
            $userMessages = ChatMessage::where('user_id', Auth::id())
                ->where('sender_type', 'user')
                ->orderBy('created_at', 'asc')
                ->get();

            // Ambil pesan pertama dari setiap percakapan untuk dijadikan judul
            $firstMessages = $userMessages->unique('conversation_id');

            // Ubah format data dan urutkan dari yang terbaru
            $conversations = $firstMessages->map(function ($message) {
                return [
                    'conversation_id' => $message->conversation_id,
                    'title' => Str::limit($message->message_text, 35), // Batasi judul agar rapi
                    'created_at' => $message->created_at,
                ];
            })->sortByDesc('created_at');

            // Kelompokkan berdasarkan tanggal untuk ditampilkan di sidebar
            $grouped = $conversations->groupBy(function ($item) {
                return $item['created_at']->format('Y-m-d');
            });

            return response()->json($grouped);

        } catch (\Exception $e) {
            Log::error('CRASH di RelaxMateController@getConversationGroups', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memuat grup percakapan.'], 500);
        }
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
            // Simpan pesan pengguna (asumsi service ini hanya menyimpan)
            $this->relaxMateService->saveMessage($user->id, $conversationId, 'user', $userMessage);
            
            // Dapatkan balasan dari AI
            $aiResponse = $this->relaxMateService->getAiReply($user, $conversationId);

            $aiMessage = null;
            if (!empty($aiResponse['reply'])) {
                // [PERBAIKAN KRITIS] Simpan balasan AI dan dapatkan modelnya untuk dikirim kembali
                // Asumsi: saveMessage mengembalikan instance ChatMessage yang baru dibuat.
                $aiMessage = $this->relaxMateService->saveMessage(
                    $user->id,
                    $conversationId,
                    'ai',
                    $aiResponse['reply'],
                    $aiResponse['metadata'] ?? null
                );
            }

            if (!$aiMessage) {
                // Fallback jika AI tidak memberikan balasan
                return response()->json(['error' => 'AI tidak dapat memberikan balasan saat ini.'], 503);
            }
            
            // [PERBAIKAN KRITIS] Kirim kembali objek ChatMessage yang lengkap dalam format yang diharapkan frontend
            return response()->json([
                'reply' => $aiMessage 
            ]);

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

