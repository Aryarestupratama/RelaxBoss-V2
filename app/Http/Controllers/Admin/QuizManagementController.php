<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizManagementController extends Controller
{
    /**
     * Menampilkan halaman daftar semua kuis.
     */
    public function index()
    {
        $quizzes = Quiz::latest()->paginate(10);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Menyimpan kuis baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:quizzes',
            'description' => 'nullable|string',
            'score_multiplier' => 'required|numeric|min:0',
        ]);

        Quiz::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'score_multiplier' => $validated['score_multiplier'],
        ]);

        return redirect()->route('admin.quizzes.index')->with('success', 'Kuis baru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data kuis.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:quizzes,name,' . $quiz->id,
            'description' => 'nullable|string',
            'score_multiplier' => 'required|numeric|min:0',
        ]);

        $quiz->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'score_multiplier' => $validated['score_multiplier'],
        ]);

        return redirect()->route('admin.quizzes.index')->with('success', 'Data kuis berhasil diperbarui.');
    }

    /**
     * [BARU] Menampilkan halaman detail untuk mengelola satu kuis.
     * Di sini admin akan mengelola pertanyaan, opsi, dan aturan skor.
     */
    public function show(Quiz $quiz)
    {
        // Eager load relasi yang dibutuhkan
        $quiz->load('questions', 'likertOptions', 'scoringRules', 'attempts.user');

        // [BARU] Ambil daftar sub-skala yang unik dari pertanyaan-pertanyaan kuis ini
        $subScales = $quiz->questions->pluck('sub_scale')->unique()->values();

        return view('admin.quizzes.manage', compact('quiz', 'subScales'));
    }

    /**
     * Menghapus kuis.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Kuis berhasil dihapus.');
    }
}