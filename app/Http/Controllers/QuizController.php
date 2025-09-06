<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question; 
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
        
        // [DIUBAH] Eager load relasi questions untuk mendapatkan sub_scale
        $quizzes = Quiz::with(['questions' => function ($query) {
                // Ambil hanya kolom sub_scale yang unik untuk efisiensi
                $query->select('quiz_id', 'sub_scale')->distinct();
            }])
            ->withCount('questions')
            ->latest()
            ->get();

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
            // [BARU] Lampirkan daftar sub_scale ke setiap objek kuis untuk digunakan di AlpineJS
            $quiz->sub_scales = $quiz->questions->pluck('sub_scale');
        });

        // [BARU] Ambil semua kategori sub_scale yang unik dari tabel questions untuk filter
        $subScaleCategories = Question::pluck('sub_scale')->unique()->sort()->values();

        return view('user.quizzes.index', compact('quizzes', 'subScaleCategories'));
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

        $filteredAnswers = array_filter($answers, function($value, $key) {
            return is_numeric($key) && $key > 0 && !is_null($value);
        }, ARRAY_FILTER_USE_BOTH);

        if (count($filteredAnswers) < $quiz->questions->count()) {
            return back()->withInput()->with('error', 'Harap jawab semua pertanyaan sebelum melanjutkan.');
        }

        $attempt = QuizAttempt::create(['quiz_id' => $quiz->id, 'user_id' => Auth::id()]);
        
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
            // [PERBAIKAN KUNCI] Tambahkan pengecekan untuk "Parah" dan "Sangat Parah"
            $interpretation = strtolower($result['interpretation']);
            if (str_contains($interpretation, 'sedang') || 
                str_contains($interpretation, 'berat') || 
                str_contains($interpretation, 'tinggi') ||
                str_contains($interpretation, 'parah')) {
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
    public function showResult(QuizAttempt $attempt, QuizAiRecommendationService $recommender) // <-- 1. Inject service AI
    {
        $this->authorize('view', $attempt);

        // [PERBAIKAN KUNCI] Cek apakah rekomendasi AI belum ada.
        if (is_null($attempt->ai_recommendation) || is_null($attempt->ai_summary)) {
            // Jika belum ada, panggil AI untuk membuatnya sekarang (tanpa konteks).
            $aiResponse = $recommender->generate($attempt);
            
            // Simpan hasilnya ke database agar tidak perlu dibuat lagi di masa depan.
            $attempt->ai_recommendation = $aiResponse['recommendation'];
            $attempt->ai_summary = $aiResponse['summary'];
            $attempt->save();

            // Muat ulang data attempt dari database untuk memastikan data terbaru.
            $attempt->refresh();
        }

        $quiz = $attempt->quiz;
        $results = $attempt->results;

        if (is_null($results)) {
            Log::warning("Attempt #{$attempt->id} diakses tanpa hasil kalkulasi.");
            return redirect()->route('quizzes.index')->with('error', 'Hasil untuk kuis ini belum tersedia atau belum selesai diproses.');
        }

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

    public function history()
    {
        $attempts = QuizAttempt::where('user_id', Auth::id())
            ->with('quiz') // Eager load nama kuis untuk efisiensi
            ->latest() // Tampilkan yang paling baru di atas
            ->paginate(10);

        return view('user.quizzes.history', compact('attempts'));
    }
}
