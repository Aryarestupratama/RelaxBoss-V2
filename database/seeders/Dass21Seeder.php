<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class Dass21Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Buat Kuis Utama (DASS-21)
        $quiz = Quiz::create([
            'name' => 'DASS 21 (Depression Anxiety Stress Scale)',
            'slug' => Str::slug('DASS 21 Depression Anxiety Stress Scale'),
            'description' => 'DASS 21 adalah satu set dari tiga skala self-report yang dirancang untuk mengukur keadaan emosional negatif dari depresi, kecemasan, dan stres. Jawablah setiap pertanyaan berdasarkan pengalaman Anda selama seminggu terakhir.',
            'score_multiplier' => 2.00, // Skor akhir untuk setiap sub-skala dikalikan 2
        ]);

        // 2. Buat Pilihan Jawaban (Skala Likert)
        $likertOptions = [
            ['label' => 'Tidak berlaku untuk saya sama sekali', 'value' => 0],
            ['label' => 'Berlaku untuk saya sampai tingkat tertentu, atau kadang-kadang', 'value' => 1],
            ['label' => 'Berlaku untuk saya sampai tingkat yang cukup berarti, atau sering', 'value' => 2],
            ['label' => 'Sangat berlaku untuk saya, atau hampir setiap saat', 'value' => 3],
        ];

        foreach ($likertOptions as $option) {
            $quiz->likertOptions()->create($option);
        }

        // 3. Buat Pertanyaan
        $questions = [
            // Stres
            ['text' => 'Saya merasa sulit untuk bersantai.', 'sub_scale' => 'Stres'], // 1
            ['text' => 'Saya cenderung bereaksi berlebihan terhadap situasi.', 'sub_scale' => 'Stres'], // 6
            ['text' => 'Saya merasa sangat gelisah.', 'sub_scale' => 'Stres'], // 8
            ['text' => 'Saya merasa sulit untuk tenang setelah sesuatu membuat saya kesal.', 'sub_scale' => 'Stres'], // 11
            ['text' => 'Saya merasa tidak bisa mentolerir apa pun yang menghalangi saya untuk melanjutkan apa yang sedang saya lakukan.', 'sub_scale' => 'Stres'], // 12
            ['text' => 'Saya merasa mudah tersinggung.', 'sub_scale' => 'Stres'], // 14
            ['text' => 'Saya merasa sangat peka atau mudah tersentuh.', 'sub_scale' => 'Stres'], // 18

            // Kecemasan
            ['text' => 'Saya menyadari mulut saya terasa kering.', 'sub_scale' => 'Kecemasan'], // 2
            ['text' => 'Saya mengalami kesulitan bernapas (misalnya, napas terlalu cepat, sesak napas tanpa aktivitas fisik).', 'sub_scale' => 'Kecemasan'], // 4
            ['text' => 'Saya mengalami gemetar (misalnya, di tangan).', 'sub_scale' => 'Kecemasan'], // 7
            ['text' => 'Saya khawatir tentang situasi di mana saya mungkin panik dan mempermalukan diri sendiri.', 'sub_scale' => 'Kecemasan'], // 9
            ['text' => 'Saya merasa dekat dengan kepanikan.', 'sub_scale' => 'Kecemasan'], // 15
            ['text' => 'Saya menyadari detak jantung saya tanpa adanya aktivitas fisik (misalnya, peningkatan detak jantung, detak jantung berhenti sejenak).', 'sub_scale' => 'Kecemasan'], // 19
            ['text' => 'Saya merasa takut tanpa alasan yang jelas.', 'sub_scale' => 'Kecemasan'], // 20

            // Depresi
            ['text' => 'Saya tidak bisa merasakan perasaan positif sama sekali.', 'sub_scale' => 'Depresi'], // 3
            ['text' => 'Saya merasa sulit untuk berinisiatif melakukan sesuatu.', 'sub_scale' => 'Depresi'], // 5
            ['text' => 'Saya merasa tidak ada hal yang bisa saya nantikan.', 'sub_scale' => 'Depresi'], // 10
            ['text' => 'Saya merasa sedih dan tertekan.', 'sub_scale' => 'Depresi'], // 13
            ['text' => 'Saya merasa tidak antusias sama sekali.', 'sub_scale' => 'Depresi'], // 16
            ['text' => 'Saya merasa diri saya tidak berharga.', 'sub_scale' => 'Depresi'], // 17
            ['text' => 'Saya merasa hidup ini tidak berarti.', 'sub_scale' => 'Depresi'], // 21
        ];

        foreach ($questions as $question) {
            $quiz->questions()->create($question);
        }

        // 4. Buat Aturan Penilaian
        $scoringRules = [
            // Depresi
            ['sub_scale' => 'Depresi', 'min_score' => 0, 'max_score' => 9, 'interpretation' => 'Normal'],
            ['sub_scale' => 'Depresi', 'min_score' => 10, 'max_score' => 13, 'interpretation' => 'Ringan'],
            ['sub_scale' => 'Depresi', 'min_score' => 14, 'max_score' => 20, 'interpretation' => 'Sedang'],
            ['sub_scale' => 'Depresi', 'min_score' => 21, 'max_score' => 27, 'interpretation' => 'Parah'],
            ['sub_scale' => 'Depresi', 'min_score' => 28, 'max_score' => 999, 'interpretation' => 'Sangat Parah'], // max_score besar untuk menangkap semua di atasnya
            
            // Kecemasan
            ['sub_scale' => 'Kecemasan', 'min_score' => 0, 'max_score' => 7, 'interpretation' => 'Normal'],
            ['sub_scale' => 'Kecemasan', 'min_score' => 8, 'max_score' => 9, 'interpretation' => 'Ringan'],
            ['sub_scale' => 'Kecemasan', 'min_score' => 10, 'max_score' => 14, 'interpretation' => 'Sedang'],
            ['sub_scale' => 'Kecemasan', 'min_score' => 15, 'max_score' => 19, 'interpretation' => 'Parah'],
            ['sub_scale' => 'Kecemasan', 'min_score' => 20, 'max_score' => 999, 'interpretation' => 'Sangat Parah'],

            // Stres
            ['sub_scale' => 'Stres', 'min_score' => 0, 'max_score' => 14, 'interpretation' => 'Normal'],
            ['sub_scale' => 'Stres', 'min_score' => 15, 'max_score' => 18, 'interpretation' => 'Ringan'],
            ['sub_scale' => 'Stres', 'min_score' => 19, 'max_score' => 25, 'interpretation' => 'Sedang'],
            ['sub_scale' => 'Stres', 'min_score' => 26, 'max_score' => 33, 'interpretation' => 'Parah'],
            ['sub_scale' => 'Stres', 'min_score' => 34, 'max_score' => 999, 'interpretation' => 'Sangat Parah'],
        ];

        foreach ($scoringRules as $rule) {
            $quiz->scoringRules()->create($rule);
        }
    }
}
