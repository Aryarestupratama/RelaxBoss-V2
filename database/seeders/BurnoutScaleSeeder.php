<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BurnoutScaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Buat Kuis Utama (Skala Burnout)
        $quiz = Quiz::create([
            'name' => 'Skala Burnout (Maslach Burnout Inventory - MBI)',
            'slug' => Str::slug('Skala Burnout Maslach Burnout Inventory MBI'),
            'description' => 'Skala Burnout (MBI) adalah instrumen yang dirancang untuk mengukur tiga dimensi burnout: Kelelahan Emosional (Emotional Exhaustion), Depersonalisasi (Depersonalization), dan Pencapaian Pribadi (Personal Accomplishment). Jawablah setiap pertanyaan berdasarkan perasaan Anda terkait pekerjaan Anda.',
            'score_multiplier' => 1.00,
        ]);

        // 2. Buat Pilihan Jawaban (Berdasarkan Petunjuk Pengisian di Gambar)
        // Menggunakan skala 0-5 untuk value agar konsisten dengan praktik umum
        $likertOptions = [
            ['label' => 'Sangat tidak menggambarkan diri anda', 'value' => 0],
            ['label' => 'Tidak menggambarkan diri anda', 'value' => 1],
            ['label' => 'Agak tidak menggambarkan diri anda', 'value' => 2],
            ['label' => 'Agak menggambarkan diri anda', 'value' => 3],
            ['label' => 'Menggambarkan diri anda', 'value' => 4],
            ['label' => 'Sangat menggambarkan diri anda', 'value' => 5],
        ];

        foreach ($likertOptions as $option) {
            $quiz->likertOptions()->create($option);
        }

        // 3. Buat Pertanyaan
        $questions = [
            // (EE) Emotional Exhaustion
            ['text' => 'Saya merasa kehabisan gairah atas pekerjaan saya', 'sub_scale' => 'Emotional Exhaustion'],
            ['text' => 'Saya merasa energi saya terkuras di akhir kerja', 'sub_scale' => 'Emotional Exhaustion'],
            ['text' => 'Saya merasa sangat lelah saat saya bangun pagi hari dan berharap menemukan hari lain untuk kerja', 'sub_scale' => 'Emotional Exhaustion'],
            ['text' => 'Bekerja dengan sejumlah orang seharian benar-benar melelahkan saya', 'sub_scale' => 'Emotional Exhaustion'],
            ['text' => 'Saya merasa gagal dari pekerjaan saya', 'sub_scale' => 'Emotional Exhaustion'],
            ['text' => 'Saya merasa terhambat dalam pekerjaan saya', 'sub_scale' => 'Emotional Exhaustion'],
            ['text' => 'Saya merasa bekerja terlalu keras', 'sub_scale' => 'Emotional Exhaustion'],
            ['text' => 'Bekerja langsung berhubungan dengan orang menyebabkan tekanan pada saya', 'sub_scale' => 'Emotional Exhaustion'],
            ['text' => 'Saya merasa seperti di ujung tanduk', 'sub_scale' => 'Emotional Exhaustion'],

            // (PA) Personal Accomplishment
            ['text' => 'Saya hebat dengan, mudah memahami cara penerima merasakan sesuatu', 'sub_scale' => 'Personal Accomplishment'],
            ['text' => 'Saya dengan mudah mengatasi masalah yang saya hadapi', 'sub_scale' => 'Personal Accomplishment'],
            ['text' => 'Saya merasa saya mampu mempengaruhi orang lain dengan positif melalui pekerjaan saya', 'sub_scale' => 'Personal Accomplishment'],
            ['text' => 'Saya merasa sangat berenergi', 'sub_scale' => 'Personal Accomplishment'],
            ['text' => 'Saya dengan mudah menciptakan suasana santai', 'sub_scale' => 'Personal Accomplishment'],
            ['text' => 'Saya merasa merasa nyaman setelah bekerja dengan orang lain', 'sub_scale' => 'Personal Accomplishment'],
            ['text' => 'Saya mencapai banyak hal berharga di dalam tugas pada pekerjaan saya', 'sub_scale' => 'Personal Accomplishment'],
            ['text' => 'Dalam pekerjaan saya, Saya mengelola masalah emosi dengan tenang', 'sub_scale' => 'Personal Accomplishment'],

            // (DP) Depersonalization
            ['text' => 'Saya merasa memperlakukan orang lain seperti objek yang bukan manusia', 'sub_scale' => 'Depersonalization'],
            ['text' => 'Saya menjadi lebih kaku terhadap orang lain sejak saya bekerja', 'sub_scale' => 'Depersonalization'],
            ['text' => 'Saya khawatir pekerjaan ini merusak saya secara emosi', 'sub_scale' => 'Depersonalization'],
            ['text' => 'Saya tidak begitu peduli akan apa yang terjadi pada diri orang lain', 'sub_scale' => 'Depersonalization'],
            ['text' => 'Saya merasa orang lain menyalahkan saya atas sejumlah masalah yang mereka temui', 'sub_scale' => 'Depersonalization'],
        ];

        foreach ($questions as $question) {
            $quiz->questions()->create($question);
        }

        // 4. Buat Aturan Penilaian
        $scoringRules = [
            // Emotional Exhaustion (EE)
            ['sub_scale' => 'Emotional Exhaustion', 'min_score' => 0, 'max_score' => 16, 'interpretation' => 'Rendah'],
            ['sub_scale' => 'Emotional Exhaustion', 'min_score' => 17, 'max_score' => 26, 'interpretation' => 'Sedang'],
            ['sub_scale' => 'Emotional Exhaustion', 'min_score' => 27, 'max_score' => 999, 'interpretation' => 'Tinggi'],
            
            // Personal Accomplishment (PA)
            ['sub_scale' => 'Personal Accomplishment', 'min_score' => 0, 'max_score' => 31, 'interpretation' => 'Rendah'],
            ['sub_scale' => 'Personal Accomplishment', 'min_score' => 32, 'max_score' => 38, 'interpretation' => 'Sedang'],
            ['sub_scale' => 'Personal Accomplishment', 'min_score' => 39, 'max_score' => 999, 'interpretation' => 'Tinggi'],

            // Depersonalization (DP)
            ['sub_scale' => 'Depersonalization', 'min_score' => 0, 'max_score' => 6, 'interpretation' => 'Rendah'],
            ['sub_scale' => 'Depersonalization', 'min_score' => 7, 'max_score' => 12, 'interpretation' => 'Sedang'],
            ['sub_scale' => 'Depersonalization', 'min_score' => 13, 'max_score' => 999, 'interpretation' => 'Tinggi'],
        ];

        foreach ($scoringRules as $rule) {
            $quiz->scoringRules()->create($rule);
        }
    }
}
