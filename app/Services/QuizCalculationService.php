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

        $answers = $attempt->answers;
        $scoringRules = $attempt->quiz->scoringRules->groupBy('sub_scale');
        
        // Dapatkan nilai maksimum dari skala Likert untuk menangani pertanyaan terbalik
        $maxLikertValue = $attempt->quiz->likertOptions->max('value') ?? 4; // Default 4 jika tidak ada

        // Kelompokkan jawaban berdasarkan sub_scale dari pertanyaannya
        $answersBySubScale = $answers->groupBy('question.sub_scale');

        $finalResults = [];

        // Iterasi melalui setiap grup sub_scale untuk menghitung skor
        foreach ($answersBySubScale as $subScale => $subScaleAnswers) {
            $totalScore = 0;

            foreach ($subScaleAnswers as $answer) {
                $question = $answer->question;
                $value = $answer->value;

                // Jika pertanyaan memiliki skor terbalik, balik nilainya
                if ($question && $question->is_reversed) {
                    $totalScore += ($maxLikertValue - $value);
                } else {
                    $totalScore += $value;
                }
            }

            // Temukan interpretasi yang sesuai dari scoring_rules
            $interpretation = $this->findInterpretation($scoringRules, $subScale, $totalScore);

            $finalResults[$subScale] = [
                'score' => $totalScore,
                'interpretation' => $interpretation,
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
            Log::warning("Tidak ada aturan penilaian untuk sub_scale: {$subScale}");
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
