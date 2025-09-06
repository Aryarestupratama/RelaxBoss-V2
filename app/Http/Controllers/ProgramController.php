<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProgramController extends Controller
{
    /**
     * Menampilkan halaman daftar semua program yang tersedia.
     */
    public function index()
    {
        $programs = Program::where('is_active', true)
            ->with('mentor')
            ->withCount('enrolledUsers')
            ->latest()
            ->get();

        // Cek program mana saja yang sudah diikuti oleh user
        $enrolledProgramIds = Auth::check() ? Auth::user()->enrolledPrograms()->pluck('program_id')->toArray() : [];

        return view('user.programs.index', compact('programs', 'enrolledProgramIds'));
    }

    /**
     * Menampilkan halaman detail satu program (feed materi).
     */
    public function show(Program $program)
    {
        $user = Auth::user();
        
        // Cek apakah user sudah terdaftar di program ini
        $enrollment = ProgramEnrollment::where('user_id', $user->id)
            ->where('program_id', $program->id)
            ->first();

        if (!$enrollment) {
            // Jika belum terdaftar, bisa diarahkan ke halaman index atau tampilkan pesan
            return redirect()->route('programs.index')->with('error', 'Anda harus mengikuti program ini untuk melihat materinya.');
        }
        
        // Hitung sudah berapa hari user bergabung
        $daysSinceEnrollment = Carbon::parse($enrollment->created_at)->diffInDays(Carbon::now()) + 1;

        // Ambil materi yang sudah "terbuka" untuk user
        $materials = $program->materials()
            ->where('day_number', '<=', $daysSinceEnrollment)
            ->orderBy('day_number', 'asc')
            ->get();

        return view('user.programs.show', compact('program', 'materials', 'enrollment'));
    }

    public function detail(Program $program)
    {
        // ambil semua materi untuk program ini, urut berdasarkan hari
        $program->load(['materials' => function($q) {
            $q->orderBy('day_number', 'asc');
        }, 'mentor']);

        return view('user.programs.detail', compact('program'));
    }

    /**
     * Mendaftarkan pengguna ke sebuah program.
     */
    public function enroll(Program $program)
    {
        $user = Auth::user();

        // Cek apakah user sudah terdaftar sebelumnya
        $isEnrolled = $user->enrolledPrograms()->where('program_id', $program->id)->exists();

        if ($isEnrolled) {
            return back()->with('error', 'Anda sudah mengikuti program ini.');
        }

        // Daftarkan user
        ProgramEnrollment::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
        ]);

        return redirect()->route('programs.show', $program)->with('success', 'Anda berhasil mengikuti program ' . $program->name);
    }
}
