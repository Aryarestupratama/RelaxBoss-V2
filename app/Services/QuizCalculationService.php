<?php

namespace App\Services;

use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Log;

class QuizCalculationService
{
    /**
     * Metode utama untuk menghitung skor dari sebuah percobaan kuis.
     *
     * @param QuizAttempt $attempt
     * @return array Hasil perhitungan skor yang dikelompokkan per sub_scale.
     */
    public function calculate(QuizAttempt $attempt): array
    {
        // Eager load relasi yang dibutuhkan untuk efisiensi
        $attempt->load('quiz.questions', 'quiz.scoringRules', 'quiz.likertOptions', 'answers.question');

        $scoringRules = $attempt->quiz->scoringRules->groupBy('sub_scale');
        
        // Dapatkan nilai maksimum dari skala Likert untuk menangani pertanyaan terbalik
        $maxLikertValue = $attempt->quiz->likertOptions->max('value');
        if (!$maxLikertValue) {
             Log::error("Kuis {$attempt->quiz->id} tidak memiliki opsi skala Likert yang valid.");
             return [];
        }

        // Kelompokkan jawaban berdasarkan sub_scale dari pertanyaannya
        $answersBySubScale = $attempt->answers->groupBy('question.sub_scale');
        
        $allSubScales = $attempt->quiz->questions->pluck('sub_scale')->unique();

        $finalResults = [];

        foreach ($allSubScales as $subScale) {
            $totalScore = 0;

            if ($answersBySubScale->has($subScale)) {
                foreach ($answersBySubScale[$subScale] as $answer) {
                    $question = $answer->question;
                    $value = $answer->value;

                    if ($question && $question->is_reversed) {
                        $totalScore += ($maxLikertValue - $value);
                    } else {
                        $totalScore += $value;
                    }
                }
            }

            // [PERBAIKAN KRITIS] Ambil skor maksimum langsung dari tabel scoring_rules
            $maxScoreForRule = 0;
            if (isset($scoringRules[$subScale])) {
                // Cari nilai 'max_score' tertinggi dari semua aturan untuk sub-skala ini
                $maxScoreForRule = $scoringRules[$subScale]->max('max_score');
            } else {
                Log::warning("Tidak ada aturan penilaian untuk sub_scale: {$subScale}, max_score akan menjadi 0.");
            }

            // Temukan interpretasi yang sesuai dari scoring_rules
            $interpretation = $this->findInterpretation($scoringRules, $subScale, $totalScore);

            $finalResults[$subScale] = [
                'score' => $totalScore,
                'interpretation' => $interpretation,
                'max_score' => $maxScoreForRule, // <-- Menggunakan max_score dari database
            ];
        }

        return $finalResults;
    }

    /**
     * Mencari interpretasi yang cocok berdasarkan sub_scale dan skor.
     *
     * @param \Illuminate\Support\Collection $rules
     * @param string $subScale
     * @param int $score
     * @return string
     */
    private function findInterpretation($rules, string $subScale, int $score): string
    {
        if (!isset($rules[$subScale])) {
            return 'Tidak Terdefinisi';
        }

        foreach ($rules[$subScale] as $rule) {
            if ($score >= $rule->min_score && $score <= $rule->max_score) {
                return $rule->interpretation;
            }
        }

        return 'Skor di luar jangkauan';
    }
}