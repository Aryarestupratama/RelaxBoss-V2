<?php

namespace App\Models;

use App\Enums\UserRole; // <-- Import Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'email',
        'password',
        'job_title',
        'gender',
        'birth_date',
        'profile_picture',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function enrolledPrograms()
    {
        return $this->belongsToMany(Program::class, 'program_enrollments');
    }

    /**
     * Profil profesional untuk user ini (jika dia psikolog).
     */
    public function psychologistProfile()
    {
        return $this->hasOne(PsychologistProfile::class);
    }

    /**
     * Dokumen-dokumen yang dimiliki oleh user ini (jika psikolog).
     */
    public function documents()
    {
        return $this->hasMany(PsychologistDocument::class);
    }

    /**
     * Layanan yang ditawarkan oleh user ini (jika psikolog).
     */
    public function services()
    {
        return $this->hasMany(PsychologistService::class);
    }

    /**
     * Jadwal yang dimiliki oleh user ini (jika psikolog).
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Spesialisasi yang dimiliki oleh user ini (jika psikolog).
     */
    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'psychologist_specialization');
    }

    /**
     * Sesi konsultasi di mana user ini adalah pasien.
     */
    public function consultationSessionsAsPatient()
    {
        return $this->hasMany(ConsultationSession::class, 'user_id');
    }

    /**
     * Sesi konsultasi di mana user ini adalah psikolog.
     */
    public function consultationSessionsAsPsychologist()
    {
        return $this->hasMany(ConsultationSession::class, 'psychologist_id');
    }

    public function consultationSessions()
    {
        return $this->hasMany(ConsultationSession::class, 'user_id');
    }
}
