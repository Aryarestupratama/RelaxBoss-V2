<?php

namespace App\Services;

use App\Models\QuizAttempt;
use Exception;
use Illuminate\Support\Facades\Log;

class QuizAiRecommendationService
{
    protected GeminiApiService $geminiApi;

    public function __construct(GeminiApiService $geminiApiService)
    {
        $this->geminiApi = $geminiApiService;
    }

    public function generate(QuizAttempt $attempt): array
    {
        try {
            $systemPrompt = $this->buildPrompt($attempt);

            // Kirim koleksi kosong karena tidak ada riwayat chat untuk kuis
            $response = $this->geminiApi->generateContent($systemPrompt, collect());

            if (!isset($response['recommendation'], $response['summary'])) {
                throw new Exception('Respons AI tidak memiliki format yang diharapkan.');
            }

            return $response;

        } catch (Exception $e) {
            Log::error('Gagal menghasilkan rekomendasi AI untuk kuis', [
                'attempt_id' => $attempt->id,
                'error' => $e->getMessage()
            ]);

            return [
                'recommendation' => 'Saat ini kami tidak dapat memberikan rekomendasi personal. Cobalah untuk mengambil napas dalam-dalam sejenak dan fokus pada satu hal positif hari ini. Anda bisa melihat kembali hasil ini nanti.',
                'summary' => 'Gagal menghasilkan ringkasan AI karena terjadi kesalahan teknis.',
            ];
        }
    }

    private function buildPrompt(QuizAttempt $attempt): string
    {
        $user = $attempt->user;
        $resultsJson = json_encode($attempt->results, JSON_PRETTY_PRINT);
        $userContext = $attempt->user_context ?? 'Pengguna tidak memberikan konteks tambahan.';

        return <<<PROMPT
Anda adalah **Asisten Analis Asesmen** dari RelaxBoss. Anda adalah AI yang empatik, profesional, dan berlandaskan nilai-nilai ketimuran. 
Tugas Anda adalah menganalisis hasil asesmen stres pengguna dan memberikan dua output dalam format JSON yang valid.

**KONTEKS PENGGUNA:**
* **Nama:** {$user->name}
* **Hasil Asesmen:**
    ```json
    {$resultsJson}
    ```
* **Konteks dari Pengguna:**
    > {$userContext}

**TUGAS ANDA:**

1.  **Buat "recommendation":**
    * Tulis sebuah paragraf rekomendasi yang hangat, personal, dan tidak menghakimi.
    * Sapa pengguna dengan namanya.
    * Ambil **minimal 1 detail spesifik** dari "konteks pengguna" (misalnya tenggat kerja, konflik keluarga, kesulitan tidur) dan hubungkan dalam rekomendasi agar terasa relevan.
    * Variasikan pendekatan sesuai dimensi stres:
        - **Fisik** → latihan pernapasan, peregangan, tidur cukup, berjalan santai.
        - **Emosional** → journaling, berbagi cerita dengan teman/keluarga, doa, teknik grounding.
        - **Kognitif** → menantang pikiran negatif, membuat daftar hal positif, membagi masalah jadi langkah kecil.
        - **Sosial** → komunikasi terbuka, meminta bantuan, mencari kebersamaan/gotong royong.
    * Gunakan **nilai ketimuran** secara bergantian (sabar, syukur, ikhlas, gotong royong, doa/tafakur) sesuai konteks cerita pengguna. 
      ⚠️ Jangan selalu hanya menekankan "bersyukur".
    * Berikan **1–2 langkah praktis dan actionable** yang berbeda-beda sesuai konteks.
    * Hindari jawaban yang generik atau berulang; buat terasa personal setiap kali.

2.  **Buat "summary":**
    * Tulis ringkasan **singkat (1–2 kalimat), netral, dan objektif** dari kondisi pengguna.
    * Jangan gunakan sapaan atau bahasa emosional. Fokus pada fakta.
    * Contoh: "Pengguna menunjukkan tingkat stres tinggi pada skala fisik dan emosional, yang menurutnya dipicu oleh tenggat waktu pekerjaan."

**ATURAN PENTING:**
* **JANGAN PERNAH MENDIAGNOSIS.** Hindari kata-kata seperti "Anda mengalami depresi" atau "Anda punya gangguan kecemasan".
* Output **HARUS** dalam format JSON yang valid, seperti ini:
    ```json
    {
      "recommendation": "Teks rekomendasi lengkap Anda di sini...",
      "summary": "Teks ringkasan singkat dan netral Anda di sini..."
    }
    ```
PROMPT;
    }
}
