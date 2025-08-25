<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelaxMateController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ProgramController;

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\QuizManagementController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\LikertOptionController; 
use App\Http\Controllers\Admin\ScoringRuleController;
use App\Http\Controllers\Admin\QuizAttemptController;
use App\Http\Controllers\Admin\ProgramController As AdminProgramController;
use App\Http\Controllers\Admin\ProgramMaterialController; 
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('welcome');

// Dashboard untuk user biasa (default dari Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


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
});

// Grup untuk Psikolog
Route::middleware(['auth', 'verified', 'role:psikolog'])->prefix('psikolog')->name('psikolog.')->group(function () {
    Route::get('/dashboard', function () {
        return view('psikolog.dashboard'); // Nanti kita buat view-nya
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('relaxmate')->name('relaxmate.')->group(function () {
        Route::get('/history', [RelaxMateController::class, 'getHistory'])->name('history');
        Route::post('/send', [RelaxMateController::class, 'sendMessage'])->name('send');
    });

    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [QuizController::class, 'index'])->name('index');
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

});

require __DIR__.'/auth.php';
