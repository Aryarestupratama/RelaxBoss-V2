<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PsychologistService;
use App\Models\ConsultationSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Menampilkan halaman pemesanan untuk seorang psikolog.
     */
    public function create(User $psychologist)
    {
        // Pastikan user yang diakses adalah psikolog dan tersedia
        if ($psychologist->role->value !== 'psikolog' || !$psychologist->psychologistProfile?->is_available) {
            abort(404);
        }

        $psychologist->load(['psychologistProfile', 'schedules', 'services']);

        // Ambil jadwal yang aktif dan kelompokkan berdasarkan hari
        $schedulesByDay = $psychologist->schedules->where('is_active', true)->groupBy('day_of_week');

        $onlineService = $psychologist->services->where('type', 'online')->where('is_active', true)->first();
        $offlineService = $psychologist->services->where('type', 'offline')->where('is_active', true)->first();

        return view('user.booking.create', compact('psychologist', 'schedulesByDay', 'onlineService', 'offlineService'));
    }

    /**
     * Menyimpan sesi konsultasi baru.
     */
    public function store(Request $request, User $psychologist)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'service_id' => 'required|exists:psychologist_services,id',
            'session_start_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        // 2. Ambil data yang dibutuhkan
        $service = PsychologistService::find($request->service_id);
        $sessionStartTime = Carbon::parse($request->session_start_time);
        $sessionEndTime = $sessionStartTime->copy()->addMinutes($service->duration_per_session_minutes);
        $patient = Auth::user();

        // 3. Validasi Lanjutan
        // Pastikan layanan milik psikolog yang benar
        if ($service->user_id !== $psychologist->id) {
            throw ValidationException::withMessages(['service_id' => 'Layanan yang dipilih tidak valid untuk psikolog ini.']);
        }
        // Pastikan waktu belum lewat
        if ($sessionStartTime->isPast()) {
            throw ValidationException::withMessages(['session_start_time' => 'Waktu yang dipilih sudah lewat.']);
        }
        // Pastikan slot jadwal benar-benar tersedia (PENTING!)
        if (!$this->isSlotAvailable($psychologist, $sessionStartTime, $service->type)) {
             throw ValidationException::withMessages(['session_start_time' => 'Slot jadwal tidak tersedia. Silakan pilih waktu lain.']);
        }
        // Pastikan slot belum di-booking orang lain
        $existingSession = ConsultationSession::where('psychologist_id', $psychologist->id)
            ->where('session_start_time', $sessionStartTime)
            ->whereNotIn('status', ['cancelled_by_user', 'cancelled_by_psychologist'])
            ->exists();

        if ($existingSession) {
            throw ValidationException::withMessages(['session_start_time' => 'Maaf, slot waktu ini baru saja dipesan. Silakan pilih waktu lain.']);
        }


        // 4. Simpan ke Database
        $consultation = ConsultationSession::create([
            'user_id' => $patient->id,
            'psychologist_id' => $psychologist->id,
            'session_start_time' => $sessionStartTime,
            'session_end_time' => $sessionEndTime,
            'price' => $service->price_per_session,
            'status' => 'booked', // Status awal
            'payment_status' => 'pending', // Status pembayaran awal
        ]);

        // 5. Redirect ke halaman selanjutnya (misal: konfirmasi atau pembayaran)
        // Kita akan buat halaman ini di langkah berikutnya
        return redirect()->route('booking.confirmation', $consultation->id)
                         ->with('success', 'Jadwal berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    /**
     * Helper function untuk memvalidasi ketersediaan slot di backend.
     */
    private function isSlotAvailable(User $psychologist, Carbon $startTime, string $serviceType): bool
    {
        $dayOfWeek = $startTime->locale('id')->dayName; // e.g., 'Senin', 'Selasa'
        $time = $startTime->format('H:i:s');

        $schedule = $psychologist->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('type', $serviceType)
            ->where('is_active', true)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->first();

        return $schedule !== null;
    }

    public function confirmation(ConsultationSession $session)
    {
        // Pastikan user hanya bisa melihat konfirmasi booking miliknya
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->load('psychologist.psychologistProfile', 'user');

        return view('user.booking.confirmation', compact('session'));
    }

    /**
     * Memproses pembayaran simulasi.
     */
    public function processPayment(Request $request, ConsultationSession $session)
    {
        // Pastikan user yang membayar adalah pemilik sesi
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        // Ubah status pembayaran dan booking
        $session->payment_status = 'paid';
        $session->status = 'confirmed';
        $session->save();

        // Redirect ke halaman sukses
        return redirect()->route('booking.success', $session->id);
    }

    /**
     * Menampilkan halaman setelah pembayaran berhasil.
     */
    public function paymentSuccess(ConsultationSession $session)
    {
        // Pastikan user hanya bisa melihat halaman sukses miliknya
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->load('psychologist.psychologistProfile');

        return view('user.booking.success', compact('session'));
    }
}
