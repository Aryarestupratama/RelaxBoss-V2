<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Exception;
use Illuminate\Support\Collection;

/**
 * Kelas ini bertanggung jawab penuh untuk berkomunikasi dengan Google Gemini API.
 * Ia tidak tahu apa-apa tentang chatbot atau RelaxMate, tugasnya hanya mengirim
 * data dan menerima hasilnya.
 */
class GeminiApiService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected int $timeout = 60; // Timeout dalam detik

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('Kunci API Gemini belum diatur di file .env Anda.');
        }
    }

    /**
     * Metode utama untuk mengirim permintaan ke Gemini API.
     *
     * @param string $systemPrompt Instruksi utama untuk AI.
     * @param \Illuminate\Support\Collection $history Riwayat percakapan dari database.
     * @return array Hasil response dari AI yang sudah di-decode.
     * @throws Exception Jika terjadi kegagalan komunikasi atau respons tidak valid.
     */
    public function generateContent(string $systemPrompt, Collection $history): array
    {
        try {
            $requestBody = $this->buildRequestBody($systemPrompt, $history);

            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '?key=' . $this->apiKey, $requestBody);

            $response->throw(); // Lemparkan exception jika status code 4xx atau 5xx

            $responseData = $response->json();
            $content = data_get($responseData, 'candidates.0.content.parts.0.text');

            if (is_null($content)) {
                Log::warning('Gemini API memberikan respons valid tetapi tanpa konten teks.', ['response' => $responseData]);
                throw new Exception('Menerima respons kosong dari Gemini.');
            }
            
            $decodedContent = json_decode($content, true);

            // Periksa jika hasil decode JSON valid
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gagal mem-parsing JSON dari respons Gemini.', ['raw_content' => $content]);
                throw new Exception('Menerima format JSON yang tidak valid dari Gemini.');
            }

            return $decodedContent;

        } catch (RequestException $e) {
            Log::error('Gemini API Request Exception', [
                'error' => $e->getMessage(), 
                'response_body' => $e->response->body()
            ]);
            throw new Exception('Gagal berkomunikasi dengan Gemini API.');
        } catch (Exception $e) {
            Log::error('GeminiApiService general error', ['error' => $e->getMessage()]);
            throw $e; // Lemparkan kembali untuk ditangani oleh service di atasnya
        }
    }

    /**
     * Membangun body request sesuai format yang dibutuhkan oleh Gemini API.
     */
    private function buildRequestBody(string $systemPrompt, Collection $history): array
    {
        // Mengubah koleksi Eloquent menjadi format array yang dibutuhkan Gemini
        $formattedHistory = $history->map(function ($message) {
            return [
                'role' => $message->sender_type === 'ai' ? 'model' : 'user',
                'parts' => [['text' => $message->message_text]]
            ];
        })->values()->all();

        return [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => $formattedHistory,
            'generationConfig' => [
                'temperature' => 0.7,
                'response_mime_type' => 'application/json', // Memaksa output menjadi JSON
            ]
        ];
    }
}
