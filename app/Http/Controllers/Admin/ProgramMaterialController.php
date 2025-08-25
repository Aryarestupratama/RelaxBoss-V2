<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramMaterial;
use Illuminate\Http\Request;

class ProgramMaterialController extends Controller
{
    /**
     * Menampilkan halaman untuk mengelola materi dari satu program.
     */
    public function index(Program $program)
    {
        // Eager load materi untuk ditampilkan
        $program->load('materials', 'enrollments.user');
        
        return view('admin.programs.manage_materials', compact('program'));
    }

    /**
     * Menyimpan materi baru untuk program tertentu.
     */
    public function store(Request $request, Program $program)
    {
        $validated = $request->validate([
            'day_number' => 'required|integer|min:1|unique:program_materials,day_number,NULL,id,program_id,' . $program->id,
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $program->materials()->create($validated);

        return back()->with('success', 'Materi baru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data materi.
     */
    public function update(Request $request, ProgramMaterial $material)
    {
        $validated = $request->validate([
            'day_number' => 'required|integer|min:1|unique:program_materials,day_number,' . $material->id . ',id,program_id,' . $material->program_id,
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $material->update($validated);

        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Menghapus materi.
     */
    public function destroy(ProgramMaterial $material)
    {
        $material->delete();
        return back()->with('success', 'Materi berhasil dihapus.');
    }
}