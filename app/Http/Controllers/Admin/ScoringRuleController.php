<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\ScoringRule;
use Illuminate\Http\Request;

class ScoringRuleController extends Controller
{
    /**
     * Menyimpan aturan skor baru untuk kuis tertentu.
     */
    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'sub_scale' => 'required|string|max:255',
            'min_score' => 'required|integer',
            'max_score' => 'required|integer|gte:min_score',
            'interpretation' => 'required|string|max:255',
        ]);

        $quiz->scoringRules()->create($validated);

        return back()->with('success', 'Aturan skor baru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data aturan skor.
     */
    public function update(Request $request, ScoringRule $rule)
    {
        $validated = $request->validate([
            'sub_scale' => 'required|string|max:255',
            'min_score' => 'required|integer',
            'max_score' => 'required|integer|gte:min_score',
            'interpretation' => 'required|string|max:255',
        ]);

        $rule->update($validated);

        return back()->with('success', 'Aturan skor berhasil diperbarui.');
    }

    /**
     * Menghapus aturan skor.
     */
    public function destroy(ScoringRule $rule)
    {
        $rule->delete();
        return back()->with('success', 'Aturan skor berhasil dihapus.');
    }
}