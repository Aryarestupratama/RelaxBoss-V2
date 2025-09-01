<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Specialization;
use App\Models\PsychologistProfile;
use Illuminate\Http\Request;

class PsychologistProfileController extends Controller
{
    public function index()
    {
        $psychologists = User::where('role', 'psikolog')->with('psychologistProfile')->latest()->paginate(10);
        return view('admin.psychologists.index', compact('psychologists'));
    }

    public function edit(User $psychologist)
    {
        if ($psychologist->role->value !== 'psikolog') {
            abort(404);
        }

        // [PERBAIKAN] Ambil profil jika ada. Jika tidak, buat instance baru tanpa menyimpannya.
        // Ini mencegah pembuatan profil kosong secara otomatis.
        $profile = $psychologist->psychologistProfile ?? new PsychologistProfile();
        
        $specializations = Specialization::all();
        $onlineService = $psychologist->services()->where('type', 'online')->firstOrCreate(['type' => 'online']);
        $offlineService = $psychologist->services()->where('type', 'offline')->firstOrCreate(['type' => 'offline']);
        $schedules = $psychologist->schedules()->orderBy('day_of_week')->orderBy('start_time')->get();

        return view('admin.psychologists.edit', compact('psychologist', 'profile', 'specializations', 'onlineService', 'offlineService', 'schedules'));
    }

    public function update(Request $request, User $psychologist)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'str_number' => 'required|string|max:255',
            'sipp_number' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'domicile' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'practice_location' => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0',
            'intro_template_message' => 'nullable|string',
            'is_available' => 'sometimes|boolean',
            'specializations' => 'nullable|array',
            'specializations.*' => 'exists:specializations,id',
        ]);

        // [PERBAIKAN] Menggunakan updateOrCreate yang lebih andal.
        // Ini akan mencari profil yang ada, atau membuat yang baru jika tidak ada,
        // lalu mengisinya dengan data yang sudah divalidasi.
        $psychologist->psychologistProfile()->updateOrCreate(
            ['user_id' => $psychologist->id],
            [
                'title' => $validated['title'],
                'str_number' => $validated['str_number'],
                'sipp_number' => $validated['sipp_number'],
                'bio' => $validated['bio'],
                'domicile' => $validated['domicile'],
                'education' => $validated['education'],
                'practice_location' => $validated['practice_location'],
                'years_of_experience' => $validated['years_of_experience'],
                'intro_template_message' => $validated['intro_template_message'],
                'is_available' => $request->boolean('is_available'),
            ]
        );

        $psychologist->specializations()->sync($request->input('specializations', []));

        return redirect()->route('admin.psychologists.edit', $psychologist)->with('success', 'Profil psikolog berhasil diperbarui.');
    }
}
