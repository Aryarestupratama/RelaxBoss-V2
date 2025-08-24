<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Exception;
use Illuminate\Support\Collection;

class GeminiApiService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected int $timeout = 60;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('Kunci API Gemini belum diatur di file .env Anda.');
        }
    }

    public function generateContent(string $systemPrompt, Collection $history): array
    {
        try {
            $requestBody = $this->buildRequestBody($systemPrompt, $history);

            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '?key=' . $this->apiKey, $requestBody);

            $response->throw();

            $responseData = $response->json();
            $content = data_get($responseData, 'candidates.0.content.parts.0.text');

            if (is_null($content)) {
                Log::warning('Gemini API memberikan respons valid tetapi tanpa konten teks.', ['response' => $responseData]);
                throw new Exception('Menerima respons kosong dari Gemini.');
            }
            
            $decodedContent = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gagal mem-parsing JSON dari respons Gemini.', ['raw_content' => $content]);
                throw new Exception('Menerima format JSON yang tidak valid dari Gemini.');
            }

            return $decodedContent;

        } catch (RequestException $e) {
            Log::error('Gemini API Request Exception', ['error' => $e->getMessage(), 'response_body' => $e->response->body()]);
            throw new Exception('Gagal berkomunikasi dengan Gemini API.');
        } catch (Exception $e) {
            Log::error('GeminiApiService general error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function buildRequestBody(string $systemPrompt, Collection $history): array
    {
        // [PERBAIKAN] Untuk tugas generate satu kali, lebih andal menempatkan prompt utama
        // sebagai pesan 'user' pertama di dalam 'contents'.
        $contents = [
            [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]]
            ]
        ];

        // Riwayat (jika ada) akan ditambahkan setelahnya. Untuk kuis, ini akan kosong.
        $formattedHistory = $history->map(function ($message) {
            return [
                'role' => $message->sender_type === 'ai' ? 'model' : 'user',
                'parts' => [['text' => $message->message_text]]
            ];
        })->values()->all();

        return [
            // 'system_instruction' dihapus untuk pendekatan ini
            'contents' => array_merge($contents, $formattedHistory),
            'generationConfig' => [
                'temperature' => 0.7,
                'response_mime_type' => 'application/json',
            ]
        ];
    }
}
