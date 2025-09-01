<?php

namespace App\Http\Controllers\Psikolog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function index()
    {
        $psychologist = Auth::user();

        // Ambil semua sesi yang ditugaskan ke psikolog ini
        $sessions = $psychologist->consultationSessionsAsPsychologist()
                                 ->with('user') // Eager load data pasien
                                 ->orderBy('session_start_time', 'desc')
                                 ->get();

        // Pisahkan sesi menjadi yang akan datang dan riwayat
        $upcomingSessions = $sessions->where('status', 'confirmed')
                                     ->where('session_start_time', '>=', now());

        $pastSessions = $sessions->where('status', '!=', 'confirmed')
                                 ->where('status', '!=', 'booked')
                                 ->merge($sessions->where('session_start_time', '<', now()));

        return view('psikolog.consultations.index', compact('upcomingSessions', 'pastSessions'));
    }

    /**
     * [BARU] Menampilkan halaman untuk melihat atau menambah catatan sesi.
     */
    public function showNote(ConsultationSession $session)
    {
        // Otorisasi: Pastikan psikolog yang login adalah pemilik sesi ini
        if ($session->psychologist_id !== Auth::id()) {
            abort(403);
        }

        // Muat relasi user (pasien) dan catatan yang sudah ada
        $session->load('user', 'note');

        return view('psikolog.consultations.note', compact('session'));
    }

    /**
     * [BARU] Menyimpan atau memperbarui catatan sesi.
     */
    public function storeNote(Request $request, ConsultationSession $session)
    {
        // Otorisasi
        if ($session->psychologist_id !== Auth::id()) {
            abort(403);
        }

        // Validasi
        $request->validate([
            'notes' => 'required|string|min:10',
        ]);

        // Gunakan updateOrCreate untuk membuat catatan baru atau memperbarui yang sudah ada
        ConsultationNote::updateOrCreate(
            ['consultation_session_id' => $session->id],
            ['notes' => $request->notes]
        );

        return redirect()->route('psikolog.consultations.index')->with('success', 'Catatan sesi berhasil disimpan.');
    }
}
