<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultationSession;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    /**
     * Menampilkan halaman ruang chat konsultasi.
     */
    public function show(ConsultationSession $session)
    {
        $user = Auth::user();

        // 1. Otorisasi: Pastikan user yang login adalah pasien atau psikolog dari sesi ini.
        if ($user->id !== $session->user_id && $user->id !== $session->psychologist_id) {
            abort(403, 'Anda tidak memiliki akses ke sesi ini.');
        }

        // 2. Validasi Status: Pastikan sesi sudah dikonfirmasi.
        if ($session->status !== 'confirmed') {
            return redirect()->route('dashboard')->with('error', 'Sesi ini belum dikonfirmasi atau sudah selesai.');
        }

        // 3. Muat data yang diperlukan
        // Kita memuat relasi 'chats' dan pengirimnya ('sender')
        $session->load(['chats.sender', 'user', 'psychologist']);

        return view('user.consultation.show', compact('session'));
    }
}
