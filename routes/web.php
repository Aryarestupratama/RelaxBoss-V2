<?php

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelaxMateController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\PsychologistController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ConsultationController;

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\QuizManagementController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\LikertOptionController; 
use App\Http\Controllers\Admin\ScoringRuleController;
use App\Http\Controllers\Admin\QuizAttemptController;
use App\Http\Controllers\Admin\ProgramController As AdminProgramController;
use App\Http\Controllers\Admin\ProgramMaterialController; 
use App\Http\Controllers\Admin\SpecializationController;
use App\Http\Controllers\Admin\PsychologistProfileController;
use App\Http\Controllers\Admin\PsychologistServiceController;
use App\Http\Controllers\Admin\ScheduleController;

use App\Http\Controllers\Psikolog\DashboardController As PsychologistDashboardController;
use App\Http\Controllers\Psikolog\ProgramController As PsychologistProgramController;
use App\Http\Controllers\Psikolog\ProgramMaterialController As PsychologistMaterialController;
use App\Http\Controllers\Psikolog\ConsultationController As PsychologistConsultationController;
use App\Http\Controllers\Psikolog\ProfileController As PsychologistPrivateProfileController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard'); 


// --- GRUP ROUTE BERDASARKAN ROLE ---

// Grup untuk Admin
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // Nanti kita buat view-nya
    })->name('dashboard');

    Route::resource('users', UserManagementController::class);
    Route::resource('quizzes', QuizManagementController::class);

    Route::prefix('quizzes/{quiz}/questions')->name('quizzes.questions.')->group(function () {
        Route::post('/', [QuestionController::class, 'store'])->name('store');
    });
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    Route::prefix('quizzes/{quiz}/options')->name('quizzes.options.')->group(function () {
        Route::post('/', [LikertOptionController::class, 'store'])->name('store');
    });
    Route::put('/options/{option}', [LikertOptionController::class, 'update'])->name('options.update');
    Route::delete('/options/{option}', [LikertOptionController::class, 'destroy'])->name('options.destroy');

    Route::prefix('quizzes/{quiz}/rules')->name('quizzes.rules.')->group(function () {
        Route::post('/', [ScoringRuleController::class, 'store'])->name('store');
    });
    Route::put('/rules/{rule}', [ScoringRuleController::class, 'update'])->name('rules.update');
    Route::delete('/rules/{rule}', [ScoringRuleController::class, 'destroy'])->name('rules.destroy');

    Route::get('attempts/{attempt}', [QuizAttemptController::class, 'show'])->name('attempts.show');

    Route::resource('programs', AdminProgramController::class);

    Route::prefix('programs/{program}/materials')->name('programs.materials.')->group(function () {
        Route::get('/', [ProgramMaterialController::class, 'index'])->name('index');
        Route::post('/', [ProgramMaterialController::class, 'store'])->name('store');
    });
    
    // Route untuk update dan delete materi
    Route::put('/materials/{material}', [ProgramMaterialController::class, 'update'])->name('materials.update');
    Route::delete('/materials/{material}', [ProgramMaterialController::class, 'destroy'])->name('materials.destroy');

    Route::resource('specializations', SpecializationController::class);

     Route::get('psychologists', [PsychologistProfileController::class, 'index'])->name('psychologists.index');
    Route::get('psychologists/{psychologist}/edit', [PsychologistProfileController::class, 'edit'])->name('psychologists.edit');
    Route::put('psychologists/{psychologist}', [PsychologistProfileController::class, 'update'])->name('psychologists.update');

    Route::post('psychologists/{psychologist}/services', [PsychologistServiceController::class, 'storeOrUpdate'])->name('psychologists.services.store');

    Route::post('psychologists/{psychologist}/schedules', [ScheduleController::class, 'store'])->name('psychologists.schedules.store');
    Route::delete('schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    Route::get('/consultations/{session}/records', [PsychologistConsultationController::class, 'showRecords'])->name('consultations.records');
});

// Grup untuk Psikolog
Route::middleware(['auth', 'verified', 'role:psikolog'])->prefix('psikolog')->name('psikolog.')->group(function () {
   Route::get('/dashboard', [PsychologistDashboardController::class, 'index'])->name('dashboard');
    
    // Route untuk halaman "Program Saya"
    Route::get('/programs', [PsychologistProgramController::class, 'index'])->name('programs.index');

    // [BARU] Route untuk manajemen materi
    Route::prefix('programs/{program}/materials')->name('programs.materials.')->group(function () {
        Route::get('/', [PsychologistMaterialController::class, 'index'])->name('index');
        Route::post('/', [PsychologistMaterialController::class, 'store'])->name('store');
    });
    Route::put('/materials/{material}', [PsychologistMaterialController::class, 'update'])->name('materials.update');
    Route::delete('/materials/{material}', [PsychologistMaterialController::class, 'destroy'])->name('materials.destroy');

    Route::get('/consultations', [PsychologistConsultationController::class, 'index'])->name('consultations.index');

    Route::get('/profile/settings', [PsychologistPrivateProfileController::class, 'edit'])->name('profile.settings');
    Route::patch('/profile/settings', [PsychologistPrivateProfileController::class, 'update'])->name('profile.update');

    // [BARU] Route untuk menampilkan form/halaman catatan sesi
    Route::get('/consultations/{session}/note', [PsychologistConsultationController::class, 'showNote'])->name('consultations.note');

    // [BARU] Route untuk menyimpan atau memperbarui catatan sesi
    Route::post('/consultations/{session}/note', [PsychologistConsultationController::class, 'storeNote'])->name('consultations.note.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::controller(RelaxMateController::class)->prefix('relaxmate')->name('relaxmate.')->group(function () {
        Route::get('/', 'index')->name('index');                 // Menampilkan halaman chat
        Route::get('/history', 'getHistory')->name('history');   // Mengambil riwayat (sesuai controller)
        Route::post('/send', 'sendMessage')->name('send');       // Mengirim pesan (sesuai controller)
        Route::delete('/clear', 'clearConversation')->name('clear'); // [BARU] Menghapus percakapan
        Route::get('/groups', 'getConversationGroups')->name('groups');
        Route::get('/latest', 'getLatestConversation')->name('latest');
    });

    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [QuizController::class, 'index'])->name('index');
        Route::get('/history', [QuizController::class, 'history'])->name('history');
        Route::get('/{quiz:slug}/introduction', [QuizController::class, 'showIntroduction'])->name('introduction');
        Route::get('/{quiz:slug}', [QuizController::class, 'show'])->name('show');
        Route::post('/{quiz}/submit', [QuizController::class, 'submit'])->name('submit');
        Route::get('/result/{attempt}', [QuizController::class, 'showResult'])->name('result');
        Route::get('/context/{attempt}', [QuizController::class, 'showContextForm'])->name('context');
        Route::post('/context/{attempt}', [QuizController::class, 'submitContext'])->name('context.submit');
        Route::get('/result/{attempt}/download', [QuizController::class, 'downloadResultPdf'])->name('result.pdf');
    });

    Route::prefix('programs')->name('programs.')->group(function () {
        // Halaman daftar semua program
        Route::get('/', [ProgramController::class, 'index'])->name('index');
        
        // Halaman detail satu program (setelah mendaftar)
        Route::get('/{program:slug}', [ProgramController::class, 'show'])->name('show');
        
        // Endpoint untuk mendaftar ke program
        Route::post('/{program}/enroll', [ProgramController::class, 'enroll'])->name('enroll');
    });

    Route::get('/psychologists', [PsychologistController::class, 'index'])->name('psychologists.index');
    Route::get('/psychologists/{psychologist}', [PsychologistController::class, 'show'])->name('psychologists.show');
    Route::get('/psychologists/{psychologist}/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/{psychologist}', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/confirmation/{session}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::post('/booking/pay/{session}', [BookingController::class, 'processPayment'])->name('booking.pay');
    Route::get('/booking/success/{session}', [BookingController::class, 'paymentSuccess'])->name('booking.success');

    Route::get('/consultation/{session}', [ConsultationController::class, 'show'])->name('consultation.show');

});

require __DIR__.'/auth.php';
