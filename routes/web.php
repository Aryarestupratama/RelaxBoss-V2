<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelaxMateController;
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
});

require __DIR__.'/auth.php';
