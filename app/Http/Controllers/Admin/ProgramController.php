<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    /**
     * Menampilkan halaman daftar semua program.
     */
    public function index()
    {
        $programs = Program::with('mentor')->latest()->paginate(10);
        // Ambil daftar psikolog untuk dropdown di modal
        $mentors = User::where('role', UserRole::PSIKOLOG)->get();

        return view('admin.programs.index', compact('programs', 'mentors'));
    }

    /**
     * Menyimpan program baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:programs',
            'description' => 'required|string',
            'duration_days' => 'required|integer|min:1',
            'mentor_id' => 'required|exists:users,id',
        ]);

        Program::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'duration_days' => $validated['duration_days'],
            'mentor_id' => $validated['mentor_id'],
        ]);

        return redirect()->route('admin.programs.index')->with('success', 'Program baru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data program.
     */
    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:programs,name,' . $program->id,
            'description' => 'required|string',
            'duration_days' => 'required|integer|min:1',
            'mentor_id' => 'required|exists:users,id',
        ]);

        $program->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'duration_days' => $validated['duration_days'],
            'mentor_id' => $validated['mentor_id'],
        ]);

        return redirect()->route('admin.programs.index')->with('success', 'Data program berhasil diperbarui.');
    }

    /**
     * Menghapus program.
     */
    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('admin.programs.index')->with('success', 'Program berhasil dihapus.');
    }
}