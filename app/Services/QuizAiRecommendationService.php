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
Anda adalah **Asisten Analis Asesmen** dari RelaxBoss. Anda adalah AI yang empatik, profesional, dan berlandaskan nilai-nilai ketimuran. Tugas Anda adalah menganalisis hasil asesmen stres pengguna dan memberikan dua output dalam format JSON yang valid.

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
    * Akui dan validasi perasaan mereka berdasarkan hasil skor dan cerita mereka.
    * Berikan 1-2 langkah praktis dan actionable yang bisa mereka coba, berdasarkan prinsip CBT (misalnya, menantang pikiran negatif, menjadwalkakan aktivitas menyenangkan).
    * Gunakan lensa nilai-nilai ketimuran (sabar, syukur, ikhlas) dalam memberikan saran.
    * Jika ada konteks dari pengguna, **WAJIB** rujuk cerita tersebut dalam rekomendasi Anda agar terasa sangat personal.

2.  **Buat "summary":**
    * Tulis sebuah ringkasan **singkat (1-2 kalimat), netral, dan objektif** dari kondisi pengguna.
    * Ini akan digunakan sebagai catatan "rekam medis" untuk dilihat oleh psikolog.
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
