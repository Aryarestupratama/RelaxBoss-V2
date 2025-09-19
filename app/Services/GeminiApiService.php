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

    public function generateContent(string $systemPrompt, Collection $history, ?array $tools = null): array
    {
        // [DIUBAH] Memanggil metode buildRequestBody yang baru
        $requestBody = $this->buildRequestBody($systemPrompt, $history, $tools);
        Log::debug('Gemini API Request Body:', $requestBody);

        try {
            $response = Http::timeout($this->timeout)
                ->retry(3, 100, function ($exception, $request) {
                    return $exception instanceof RequestException && in_array($exception->response->status(), [429, 500, 503]);
                }, throw: false)
                ->post($this->apiUrl . '?key=' . $this->apiKey, $requestBody);
            
            $response->throw();

            $responseData = $response->json();
            Log::debug('Gemini API Response Body:', $responseData);

            $part = data_get($responseData, 'candidates.0.content.parts.0');

            if (is_null($part)) {
                throw new Exception('Menerima respons kosong dari Gemini.');
            }
            
            if (isset($part['functionCall'])) {
                return $part;
            }

            if (isset($part['text'])) {
                $decoded = json_decode($part['text'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
                return ['text' => $part['text']];
            }

            return $part;

        } catch (RequestException $e) {
            Log::error('Gemini API Request Exception', [
                'error' => $e->getMessage(),
                'response_body' => $e->response?->body()
            ]);
            if ($e->response && $e->response->status() === 503) {
                throw new Exception('Layanan AI sedang sibuk. Silakan coba lagi sebentar.');
            }
            throw new Exception('Gagal berkomunikasi dengan Gemini API setelah beberapa kali percobaan.');
        } catch (Exception $e) {
            Log::error('GeminiApiService general error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * [BARU & DIPERBAIKI] Membangun request body untuk Gemini API menggunakan systemInstruction.
     */
    private function buildRequestBody(string $systemPrompt, Collection $history, ?array $tools): array
    {
        // Format riwayat percakapan
        $formattedHistory = $history->map(function ($message) {
            if (($message['role'] ?? null) === 'function') {
                return [
                    'role' => 'function',
                    'parts' => [['functionResponse' => [
                        'name' => $message['name'],
                        'response' => ['content' => $message['content']]
                    ]]]
                ];
            }
            return [
                'role' => ($message['sender_type'] ?? '') === 'ai' ? 'model' : 'user',
                'parts' => [['text' => $message['message_text'] ?? '']]
            ];
        })->values()->all();

        $body = [
            'contents' => $formattedHistory,
            'systemInstruction' => [ // Menggunakan bidang khusus untuk instruksi sistem
                'parts' => [['text' => $systemPrompt]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'response_mime_type' => 'application/json',
            ]
        ];

        if (!empty($tools)) {
            $body['tools'] = $tools;
        }

        return $body;
    }
}

