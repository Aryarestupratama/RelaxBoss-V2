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
            'text' => 'required|string|max:255',
            'sub_scale' => 'required|string|max:255',
            // Validasi untuk is_reversed tidak lagi diperlukan di sini
        ]);

        // 1. Update field teks biasa
        $question->text = $validated['text'];
        $question->sub_scale = $validated['sub_scale'];

        // 2. [PERBAIKAN] Update field boolean dengan cara yang paling andal.
        // $request->has('is_reversed') akan mengembalikan `true` jika checkbox dicentang
        // dan `false` jika tidak dicentang (karena browser tidak mengirimkannya).
        $question->is_reversed = $request->has('is_reversed');

        // 3. Simpan semua perubahan
        $question->save();

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