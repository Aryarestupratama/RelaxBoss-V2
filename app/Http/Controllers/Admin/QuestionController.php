<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Menyimpan pertanyaan baru untuk kuis tertentu.
     */
    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'sub_scale' => 'required|string|max:255',
            'is_reversed' => 'sometimes|boolean',
        ]);

        $quiz->questions()->create([
            'text' => $validated['text'],
            'sub_scale' => $validated['sub_scale'],
            'is_reversed' => $request->has('is_reversed') ? true : false,
        ]);

        return back()->with('success', 'Pertanyaan baru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data pertanyaan.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'sub_scale' => 'required|string|max:255',
            'is_reversed' => 'sometimes|boolean',
        ]);

        $question->update([
            'text' => $validated['text'],
            'sub_scale' => $validated['sub_scale'],
            'is_reversed' => $request->has('is_reversed') ? true : false,
        ]);

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Menghapus pertanyaan.
     */
    public function destroy(Question $question)
    {
        $question->delete();
        return back()->with('success', 'Pertanyaan berhasil dihapus.');
    }
}