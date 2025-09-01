<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use App\Models\ConsultationSession; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama untuk pengguna.
     */
    public function index()
    {
        $user = Auth::user();

        // --- Data Asesmen (Sudah Ada) ---
        $attempts = QuizAttempt::where('user_id', $user->id)
            ->whereNotNull('results')
            ->latest()
            ->get();
        $latestAttempt = $attempts->first();
        $chartData = $this->prepareChartData($attempts);

        // --- Data Statistik (Sudah Ada) ---
        $stats = [
            'assessments_taken' => $attempts->count(),
            'chatbot_sessions' => 0, 
            'programs_joined' => $user->enrolledPrograms()->count(),
        ];

        // --- [BARU] Ambil Data Sesi Konsultasi ---
        $upcomingSession = $user->consultationSessions()
                                ->with('psychologist')
                                ->where('status', 'confirmed')
                                ->where('session_start_time', '>=', now())
                                ->orderBy('session_start_time', 'asc')
                                ->first(); // Ambil satu sesi terdekat saja untuk ditampilkan

        // --- Kirim semua data ke view ---
        return view('dashboard', compact(
            'user', 
            'latestAttempt', 
            'stats', 
            'chartData', 
            'upcomingSession' // Tambahkan data sesi
        ));
    }

    /**
     * Memformat data pengerjaan kuis untuk Chart.js.
     */
    private function prepareChartData($attempts)
    {
        $sortedAttempts = $attempts->reverse();
        $labels = [];
        $data = [];

        foreach ($sortedAttempts as $attempt) {
            $labels[] = Carbon::parse($attempt->created_at)->format('d M');
            $highestScore = 0;
            if (is_array($attempt->results)) {
                $highestScore = collect($attempt->results)->max('score') ?? 0;
            }
            $data[] = $highestScore;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
