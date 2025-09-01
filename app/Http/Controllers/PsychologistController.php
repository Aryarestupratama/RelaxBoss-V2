<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Specialization;
use Illuminate\Http\Request;

class PsychologistController extends Controller
{
    /**
     * Menampilkan halaman daftar semua psikolog yang tersedia.
     */
    public function index()
    {
        // Ambil hanya psikolog yang profilnya tersedia (is_available = true)
        $psychologists = User::where('role', 'psikolog')
            ->whereHas('psychologistProfile', function ($query) {
                $query->where('is_available', true);
            })
            // [PERBAIKAN] Tambahkan 'services' untuk memuat data harga
            ->with(['psychologistProfile', 'specializations', 'services']) 
            ->latest()
            ->get();

        // Ambil semua bidang keahlian untuk filter
        $specializations = Specialization::all();

        return view('user.psychologists.index', compact('psychologists', 'specializations'));
    }

    public function show(User $psychologist)
    {
        // Pastikan user yang diakses adalah psikolog dan profilnya tersedia
        if ($psychologist->role->value !== 'psikolog' || !$psychologist->psychologistProfile?->is_available) {
            abort(404);
        }

        // Eager load semua relasi yang dibutuhkan untuk ditampilkan di profil
        $psychologist->load(['psychologistProfile', 'specializations', 'services', 'documents']);

        return view('user.psychologists.show', compact('psychologist'));
    }

}
