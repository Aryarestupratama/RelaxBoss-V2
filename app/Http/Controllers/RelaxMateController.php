<?php

// app/Http/Controllers/RelaxMateController.php

namespace App\Http\Controllers;

use App\Services\RelaxMateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RelaxMateController extends Controller
{
    protected RelaxMateService $relaxMateService;

    public function __construct(RelaxMateService $relaxMateService)
    {
        $this->relaxMateService = $relaxMateService;
    }

    /**
     * Mengambil riwayat percakapan untuk pengguna yang sedang login.
     */
    public function getHistory(Request $request)
    {
        try {
            // [PERBAIKAN] Pengecekan autentikasi yang lebih tegas.
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
            // [PERBAIKAN] Logging yang lebih detail untuk debugging.
            Log::error('CRASH di RelaxMateController@getHistory', [
                'user_id' => Auth::id(), // Akan null jika auth gagal
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Memberikan jejak lengkap error
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
            $this->relaxMateService->saveMessage($user->id, $conversationId, 'user', $userMessage);
            $aiResponse = $this->relaxMateService->getAiReply($user, $conversationId);

            if (isset($aiResponse['reply'])) {
                $this->relaxMateService->saveMessage(
                    $user->id,
                    $conversationId,
                    'ai',
                    $aiResponse['reply'],
                    $aiResponse['metadata'] ?? null
                );
            }

            return response()->json($aiResponse);

        } catch (\Exception $e) {
            Log::error('CRASH di RelaxMateController@sendMessage', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Maaf, terjadi kesalahan di server kami.'], 500);
        }
    }
}
