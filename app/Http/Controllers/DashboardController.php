<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama untuk pengguna.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil hasil asesmen terakhir
        $latestAttempt = QuizAttempt::where('user_id', $user->id)
            ->whereNotNull('results') // Pastikan hanya mengambil yang sudah ada hasilnya
            ->latest()
            ->first();

        // 2. Siapkan data statistik
        $stats = [
            'assessments_taken' => QuizAttempt::where('user_id', $user->id)->count(),
            // Placeholder, bisa dikembangkan nanti
            'chatbot_sessions' => 0, 
            'programs_joined' => $user->enrolledPrograms()->count(),
        ];

        return view('dashboard', compact('user', 'latestAttempt', 'stats'));
    }
}