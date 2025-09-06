<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RelaxMateService
{
    protected GeminiApiService $geminiApi;

    public function __construct(GeminiApiService $geminiApiService)
    {
        $this->geminiApi = $geminiApiService;
    }

    public function saveMessage(int $userId, string $conversationId, string $senderType, string $messageText, ?array $metadata = null)
    {
        return ChatMessage::create([
            'user_id' => $userId,
            'conversation_id' => $conversationId,
            'sender_type' => $senderType,
            'message_text' => $messageText,
            'metadata' => $metadata,
        ]);
    }

    public function getConversationHistory(int $userId, string $conversationId)
    {
        return ChatMessage::where('user_id', $userId)
            ->where('conversation_id', $conversationId)
            // [PERBAIKAN] Pastikan pesan judul tidak ikut terambil dalam riwayat chat normal
            ->where(function ($query) {
                $query->whereNull('metadata->is_title')
                      ->orWhere('metadata->is_title', '!=', true);
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getAiReply(User $user, string $conversationId)
    {
        try {
            $history = $this->getConversationHistory($user->id, $conversationId);
            $systemPrompt = $this->buildSystemPrompt($user->name);
            
            $response = $this->geminiApi->generateContent($systemPrompt, $history);

            if (!isset($response['reply'])) {
                throw new Exception('Format JSON dari AI tidak valid.');
            }

            return $response;

        } catch (Exception $e) {
            Log::error('Gagal mendapatkan balasan AI', ['error' => $e->getMessage()]);
            return [
                'reply' => "Maaf, saya sedang mengalami sedikit kendala. Bisakah Anda mengulangi pertanyaan Anda?",
                'metadata' => ['action' => 'error']
            ];
        }
    }

    /**
     * [BARU] Membuat judul ringkasan untuk percakapan dan menyimpannya.
     */
    public function generateAndSaveTitle(string $conversationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $history = $this->getConversationHistory($user->id, $conversationId);

        if (count($history) < 4) return;

        $promptForTitle = "Berdasarkan percakapan berikut, buatlah sebuah judul singkat (maksimal 5 kata) yang merangkum tema utamanya:\n\n";
        foreach ($history as $message) {
            $sender = $message->sender_type === 'user' ? 'User' : 'AI';
            $promptForTitle .= "{$sender}: {$message->message_text}\n";
        }
        
        // [PERBAIKAN] Menggunakan GeminiApiService untuk membuat judul
        $titleText = $this->getAiReplyFromText($promptForTitle); 

        if ($titleText) {
            // Cek apakah sudah ada judul, jika ada, update. Jika tidak, buat baru.
            ChatMessage::updateOrCreate(
                [
                    'conversation_id' => $conversationId,
                    'metadata->is_title' => true,
                ],
                [
                    'user_id' => $user->id,
                    'sender_type' => 'ai',
                    'message_text' => Str::remove('"', $titleText), // Hapus tanda kutip jika AI menambahkannya
                    'metadata' => ['is_title' => true],
                ]
            );
        }
    }

    /**
     * [BARU] Helper untuk memanggil AI hanya dengan teks dan mendapatkan teks kembali.
     */
    private function getAiReplyFromText(string $prompt): ?string
    {
        try {
            // Buat "pesan palsu" untuk dikirim ke API Gemini
            $fakeHistory = collect([
                new ChatMessage(['sender_type' => 'user', 'message_text' => $prompt])
            ]);

            // Gunakan system prompt yang netral
            $systemPrompt = "Anda adalah asisten yang pandai meringkas.";

            $response = $this->geminiApi->generateContent($systemPrompt, $fakeHistory);

            return $response['reply'] ?? null;

        } catch (Exception $e) {
            Log::error('Gagal membuat judul AI', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Membangun System Prompt dengan informasi dinamis.
     */
    private function buildSystemPrompt(string $userName): string
    {
        // Ganti dengan prompt lengkap yang sudah kita rancang sebelumnya.
        // Idealnya, prompt ini disimpan di file config (contoh: config/relaxmate.php)
        // agar mudah diubah tanpa menyentuh kode service.
        return <<<PROMPT
Anda adalah **RelaxMate**, seorang asisten virtual kesehatan mental dari RelaxBoss yang bijaksana, tenang, dan suportif. Anda dirancang untuk membantu para profesional mengelola stres kerja dan kehidupan dengan pendekatan yang terstruktur dan berlandaskan nilai.

---

### **PRINSIP INTI & PERSONA**

1.  **Persona Anda:** Anda adalah seorang konselor pendengar yang bijaksana. Gunakan bahasa yang tenang, penuh hormat, dan tidak menghakimi. Sapa pengguna dengan nama mereka ({$userName}).
2.  **Fokus Utama:** Manajemen stres, kecemasan kerja, *burnout*, produktivitas, dan *mindfulness*.
3.  **Sistem Nilai:** Jawaban dan bimbingan Anda harus selaras dengan **nilai-nilai ketimuran dan agamis**. Ini berarti menekankan:
    * **Kesabaran (Sabar):** Menerima proses dan tidak terburu-buru.
    * **Rasa Syukur (Syukur):** Menemukan hal positif bahkan dalam kesulitan.
    * **Penerimaan Ikhlas (Ikhlas):** Menerima hal-hal di luar kendali.
    * **Pentingnya Keluarga & Komunitas:** Mendorong dukungan sosial sebagai sumber kekuatan.
    * **Spiritualitas:** Mengakui keyakinan sebagai sumber ketenangan, tanpa memihak agama tertentu.

---

### **KERANGKA KERJA WAJIB: COGNITIVE BEHAVIORAL THERAPY (CBT)**

Anda HARUS memandu pengguna melalui percakapan menggunakan alur CBT yang disederhanakan. Jangan sebutkan "CBT" secara langsung, tetapi gunakan langkah-langkahnya secara alami dalam percakapan.

* **Langkah 1: Identifikasi Situasi (Situation):** Tanyakan apa yang sedang terjadi atau apa yang memicu stres mereka.
* **Langkah 2: Identifikasi Pikiran Otomatis (Automatic Thoughts):** Gali apa yang ada di pikiran mereka saat situasi itu terjadi.
* **Langkah 3: Identifikasi Perasaan & Perilaku (Feelings & Behaviors):** Tanyakan bagaimana pikiran itu membuat mereka merasa dan bertindak.
* **Langkah 4: Tantang & Bingkai Ulang (Challenge & Reframe):** Bantu pengguna melihat pikiran mereka dari sudut pandang lain, menggunakan **Sistem Nilai** di atas.

---

### **BATASAN ETIS & PENANGANAN TOPIK SENSITIF**

1.  **BUKAN TERAPIS:** Anda **DILARANG KERAS** memberikan diagnosis medis atau psikiatris.
2.  **ESKALASI DARURAT:** Jika pengguna menunjukkan tanda-tanda depresi berat atau menyakiti diri sendiri, **SEGERA HENTIKAN** alur CBT dan berikan respons darurat untuk menghubungi profesional.
3.  **TOPIK SENSITIF (Contoh: Pergaulan Bebas):**
    * **Validasi Perasaan, Bukan Perilaku:** Akui dan validasi **perasaan stres** yang dialami pengguna.
    * **Hindari Penghakiman & Pembenaran:** JANGAN menghakimi atau membenarkan perilakunya.
    * **Arahkan ke Nilai & Konsekuensi:** Arahkan percakapan kembali ke **dampak perilaku tersebut terhadap diri pengguna dan nilai-nilai yang mereka pegang**. Gunakan pertanyaan reflektif.
    * **Fokus pada Solusi Berbasis Nilai:** Arahkan solusi ke arah yang sesuai dengan nilai ketimuran.

---

### **FORMAT JAWABAN JSON**

Selalu berikan jawaban dalam format JSON yang valid.
`{"reply": "Teks jawaban Anda di sini...", "metadata": {"action": "none", "cbt_step": "identifying_thought"}}`
PROMPT;
    }
}
