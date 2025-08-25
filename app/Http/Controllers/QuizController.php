<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizCalculationService;
use App\Services\QuizAiRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Barryvdh\DomPDF\Facade\Pdf;

class QuizController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $quizzes = Quiz::withCount('questions')->latest()->get();
        $todaysAttempts = QuizAttempt::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->latest()
            ->get()
            ->keyBy('quiz_id');

        $quizzes->each(function ($quiz) use ($todaysAttempts) {
            $quiz->attempted_today = $todaysAttempts->has($quiz->id);
            if ($quiz->attempted_today) {
                $quiz->latest_attempt_id_today = $todaysAttempts->get($quiz->id)->id;
            }
        });

        return view('user.quizzes.index', compact('quizzes'));
    }

    public function showIntroduction(Quiz $quiz)
    {
        // Eager load relasi yang dibutuhkan
        $quiz->load('questions', 'likertOptions');

        // Ambil daftar sub-skala yang unik dari pertanyaan
        $subScales = $quiz->questions->pluck('sub_scale')->unique()->values();

        return view('user.quizzes.introduction', compact('quiz', 'subScales'));
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions', 'likertOptions');
        if ($quiz->questions->isEmpty() || $quiz->likertOptions->isEmpty()) {
            return redirect()->route('quizzes.index')->with('error', 'Kuis ini belum siap atau tidak lengkap.');
        }
        return view('user.quizzes.show', compact('quiz'));
    }

    /**
     * Menerima, memproses, menghitung, dan MENYIMPAN jawaban kuis.
     */
    public function submit(Request $request, Quiz $quiz, QuizCalculationService $calculator, QuizAiRecommendationService $recommender)
    {
        $answers = $request->input('answers', []);

        // [PERBAIKAN KUNCI] Filter data jawaban untuk memastikan validitasnya.
        // Ini akan menghapus entri yang rusak seperti [0 => null].
        $filteredAnswers = array_filter($answers, function($value, $key) {
            $isValid = is_numeric($key) && $key > 0 && !is_null($value);
            if (!$isValid) {
                Log::warning('Data jawaban tidak valid terdeteksi dan difilter.', ['key' => $key, 'value' => $value]);
            }
            return $isValid;
        }, ARRAY_FILTER_USE_BOTH);

        // Validasi menggunakan data yang sudah difilter
        if (count($filteredAnswers) < $quiz->questions->count()) {
            return back()->withInput()->with('error', 'Harap jawab semua pertanyaan sebelum melanjutkan.');
        }

        $attempt = QuizAttempt::create(['quiz_id' => $quiz->id, 'user_id' => Auth::id()]);
        
        // Gunakan data yang sudah bersih untuk menyimpan
        foreach ($filteredAnswers as $questionId => $value) {
            $attempt->answers()->create([
                'question_id' => $questionId,
                'value' => $value,
            ]);
        }

        $results = $calculator->calculate($attempt);
        $attempt->results = $results;
        $attempt->save();

        $isSevere = false;
        foreach($results as $result) {
            if (stripos($result['interpretation'], 'Sedang') !== false || stripos($result['interpretation'], 'Berat') !== false || stripos($result['interpretation'], 'Tinggi') !== false) {
                $isSevere = true;
                break;
            }
        }

        if ($isSevere) {
            return redirect()->route('quizzes.context', $attempt->id);
        } else {
            $aiResponse = $recommender->generate($attempt);
            $attempt->ai_recommendation = $aiResponse['recommendation'];
            $attempt->ai_summary = $aiResponse['summary'];
            $attempt->save();

            return redirect()->route('quizzes.result', $attempt->id);
        }
    }

    /**
     * Menampilkan halaman hasil kuis yang sudah disimpan.
     */
    public function showResult(QuizAttempt $attempt)
    {
        $this->authorize('view', $attempt);

        // [PERBAIKAN] Logika pemanggilan AI dihapus.
        // Fungsi ini sekarang hanya bertugas untuk menampilkan data yang sudah ada.

        $quiz = $attempt->quiz;
        $results = $attempt->results;

        // Cek jika hasil kalkulasi skor belum ada (sebagai pengaman)
        if (is_null($results)) {
            Log::warning("Attempt #{$attempt->id} diakses tanpa hasil kalkulasi.");
            return redirect()->route('quizzes.index')->with('error', 'Hasil untuk kuis ini belum tersedia atau belum selesai diproses.');
        }

        // Kirim semua data yang ada (termasuk ai_summary yang mungkin null) ke view.
        return view('user.quizzes.results', compact('quiz', 'results', 'attempt'));
    }

    /**
     * Menampilkan halaman untuk input konteks/cerita.
     */
    public function showContextForm(QuizAttempt $attempt)
    {
        $this->authorize('view', $attempt);
        return view('user.quizzes.context', compact('attempt'));
    }

    /**
     * Menyimpan konteks dan memicu AI untuk memberikan rekomendasi.
     */
    public function submitContext(Request $request, QuizAttempt $attempt, QuizAiRecommendationService $recommender)
    {
        $this->authorize('view', $attempt);
        $validated = $request->validate(['context' => 'required|string|min:20']);
        
        $attempt->user_context = $validated['context'];
        
        $aiResponse = $recommender->generate($attempt);
        $attempt->ai_recommendation = $aiResponse['recommendation'];
        $attempt->ai_summary = $aiResponse['summary'];
        
        $attempt->save();

        return redirect()->route('quizzes.result', $attempt->id);
    }

    public function downloadResultPdf(QuizAttempt $attempt)
    {
        $this->authorize('view', $attempt);

        // Muat data yang sama seperti di showResult
        $quiz = $attempt->quiz;
        $results = $attempt->results;

        // Buat PDF dari view khusus
        $pdf = PDF::loadView('user.quizzes.results_pdf', compact('quiz', 'results', 'attempt'));
        
        // Beri nama file dan unduh
        return $pdf->download('hasil-asesmen-'.$quiz->slug.'-'.$attempt->id.'.pdf');
    }
}
