<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    /**
     * Menampilkan detail lengkap dari satu pengerjaan kuis (attempt).
     * Ini berfungsi sebagai halaman "rekam medis" untuk admin.
     */
    public function show(QuizAttempt $attempt)
    {
        // Eager load relasi untuk ditampilkan di view
        $attempt->load('user', 'quiz');

        return view('admin.quizzes.show_attempt', compact('attempt'));
    }
}
