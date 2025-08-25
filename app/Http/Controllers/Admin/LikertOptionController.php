<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\LikertOption;
use Illuminate\Http\Request;

class LikertOptionController extends Controller
{
    /**
     * Menyimpan opsi jawaban baru untuk kuis tertentu.
     */
    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|integer',
        ]);

        $quiz->likertOptions()->create($validated);

        return back()->with('success', 'Opsi jawaban baru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data opsi jawaban.
     */
    public function update(Request $request, LikertOption $option)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|integer',
        ]);

        $option->update($validated);

        return back()->with('success', 'Opsi jawaban berhasil diperbarui.');
    }

    /**
     * Menghapus opsi jawaban.
     */
    public function destroy(LikertOption $option)
    {
        $option->delete();
        return back()->with('success', 'Opsi jawaban berhasil dihapus.');
    }
}
