<?php

namespace App\Http\Controllers\Psikolog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengedit profil, layanan, dan jadwal.
     */
    public function edit()
    {
        $psychologist = Auth::user();

        // Pastikan profil ada, jika tidak buat baru (sesuai alur dari admin)
        $profile = $psychologist->psychologistProfile()->firstOrCreate(['user_id' => $psychologist->id]);

        // Ambil layanan dan jadwal yang sudah ada
        $onlineService = $psychologist->services()->where('type', 'online')->first();
        $offlineService = $psychologist->services()->where('type', 'offline')->first();
        
        // Kelompokkan jadwal berdasarkan hari dan tipe untuk memudahkan di view
        $schedules = $psychologist->schedules->groupBy('day_of_week')->map(function ($daySchedules) {
            return $daySchedules->keyBy('type');
        });

        // Daftar hari untuk iterasi di view
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('psikolog.profile.edit', compact('psychologist', 'profile', 'onlineService', 'offlineService', 'schedules', 'days'));
    }

    /**
     * Memperbarui profil, layanan, dan jadwal.
     */
    public function update(Request $request)
    {
        $psychologist = Auth::user();

        // 1. Update Profil Profesional
        $profileData = $request->validate([
            'profile.title' => 'nullable|string|max:255',
            'profile.bio' => 'nullable|string',
            'profile.education' => 'nullable|string|max:255',
            'profile.practice_location' => 'nullable|string|max:255',
            'profile.years_of_experience' => 'nullable|integer|min:0',
            'profile.str_number' => 'nullable|string|max:255',
            'profile.sipp_number' => 'nullable|string|max:255',
        ]);
        // Handle checkbox 'is_available'
        $profileData['profile']['is_available'] = $request->has('profile.is_available');
        $psychologist->psychologistProfile()->update($profileData['profile']);

        // 2. Update Layanan & Harga
        $servicesData = $request->validate([
            'services.online.price_per_session' => 'nullable|numeric|min:0',
            'services.online.duration_per_session_minutes' => 'nullable|integer|min:1',
            'services.offline.price_per_session' => 'nullable|numeric|min:0',
            'services.offline.duration_per_session_minutes' => 'nullable|integer|min:1',
        ]);

        // Layanan Online
        $psychologist->services()->updateOrCreate(
            ['type' => 'online'],
            [
                'price_per_session' => $servicesData['services']['online']['price_per_session'] ?? 0,
                'duration_per_session_minutes' => $servicesData['services']['online']['duration_per_session_minutes'] ?? 50,
                'is_active' => $request->has('services.online.is_active'),
            ]
        );
        // Layanan Offline
        $psychologist->services()->updateOrCreate(
            ['type' => 'offline'],
            [
                'price_per_session' => $servicesData['services']['offline']['price_per_session'] ?? 0,
                'duration_per_session_minutes' => $servicesData['services']['offline']['duration_per_session_minutes'] ?? 50,
                'is_active' => $request->has('services.offline.is_active'),
            ]
        );

        // 3. Update Jadwal Praktik
        if ($request->has('schedules')) {
            foreach ($request->schedules as $day => $types) {
                foreach ($types as $type => $schedule) {
                    $isActive = isset($schedule['is_active']);
                    $startTime = $schedule['start_time'];
                    $endTime = $schedule['end_time'];

                    // Hanya proses jika jadwal aktif dan waktunya valid
                    if ($isActive && $startTime && $endTime) {
                        $psychologist->schedules()->updateOrCreate(
                            ['day_of_week' => $day, 'type' => $type],
                            [
                                'start_time' => $startTime,
                                'end_time' => $endTime,
                                'is_active' => true
                            ]
                        );
                    } else {
                        // Jika tidak aktif atau tidak valid, hapus atau nonaktifkan
                        $psychologist->schedules()
                            ->where('day_of_week', $day)
                            ->where('type', $type)
                            ->delete();
                    }
                }
            }
        }

        return redirect()->route('psikolog.profile.settings')->with('success', 'Pengaturan profil dan jadwal berhasil diperbarui.');
    }
}
