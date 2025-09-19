<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct()
    {
        // PASTIKAN ADA "$this->" DI SINI
        $this->middleware('auth:sanctum');
    }

    /**
     * Menampilkan semua proyek milik pengguna.
     */
    public function index(): JsonResponse
    {
        $projects = Auth::user()->projects()->withCount('todos')->orderBy('name')->get();
        return response()->json($projects);
    }

    /**
     * Menyimpan proyek baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Auth::user()->projects()->create($validated);
        $project->loadCount('todos'); // Muat todos_count agar datanya lengkap

        return response()->json($project, 201);
    }

    /**
     * Memperbarui proyek yang ada.
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        // Otorisasi: Pastikan pengguna hanya bisa mengedit proyek miliknya
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);
        $project->loadCount('todos');

        return response()->json($project);
    }

    /**
     * Menghapus proyek.
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        // Opsi: Hapus semua tugas di dalamnya juga.
        // Ini sudah diatur dengan 'onDelete('cascade')' di migrasi Anda,
        // jadi pemanggilan $project->todos()->delete() tidak diperlukan.
        $project->delete();

        return response()->json(['message' => 'Proyek berhasil dihapus.']);
    }
}