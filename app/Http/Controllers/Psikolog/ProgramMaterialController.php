<?php

namespace App\Http\Controllers\Psikolog;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramMaterial;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProgramMaterialController extends Controller
{
    use AuthorizesRequests;

    public function index(Program $program)
    {
        // Pastikan psikolog ini adalah pembimbing program
        $this->authorize('manage', $program);

        $program->load('materials');
        return view('psikolog.programs.manage_materials', compact('program'));
    }

    public function store(Request $request, Program $program)
    {
        $this->authorize('manage', $program);

        $validated = $request->validate([
            'day_number' => 'required|integer|min:1|unique:program_materials,day_number,NULL,id,program_id,' . $program->id,
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $program->materials()->create($validated);
        return back()->with('success', 'Materi baru berhasil ditambahkan.');
    }

    public function update(Request $request, ProgramMaterial $material)
    {
        $this->authorize('manage', $material->program);

        $validated = $request->validate([
            'day_number' => 'required|integer|min:1|unique:program_materials,day_number,' . $material->id . ',id,program_id,' . $material->program_id,
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $material->update($validated);
        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(ProgramMaterial $material)
    {
        $this->authorize('manage', $material->program);
        
        $material->delete();
        return back()->with('success', 'Materi berhasil dihapus.');
    }
}