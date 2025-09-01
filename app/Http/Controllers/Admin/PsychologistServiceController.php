<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PsychologistService;
use Illuminate\Http\Request;

class PsychologistServiceController extends Controller
{
    /**
     * Menyimpan atau mengupdate layanan untuk seorang psikolog.
     */
    public function storeOrUpdate(Request $request, User $psychologist)
    {
        $validated = $request->validate([
            'services' => 'required|array',
            'services.*.type' => 'required|in:online,offline',
            'services.*.is_active' => 'sometimes|boolean',
            'services.*.price_per_session' => 'required|numeric|min:0',
            'services.*.duration_per_session_minutes' => 'required|integer|min:15',
            'services.*.is_free' => 'sometimes|boolean',
        ]);

        foreach ($validated['services'] as $serviceData) {
            $psychologist->services()->updateOrCreate(
                ['type' => $serviceData['type']], // Kunci untuk mencari
                [ // Data untuk diupdate atau dibuat
                    'is_active' => $request->boolean('services.' . $serviceData['type'] . '.is_active'),
                    'price_per_session' => $serviceData['price_per_session'],
                    'duration_per_session_minutes' => $serviceData['duration_per_session_minutes'],
                    'is_free' => $request->boolean('services.' . $serviceData['type'] . '.is_free'),
                ]
            );
        }

        return back()->with('success', 'Layanan konsultasi berhasil diperbarui.');
    }
}
