<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'psychologist_id',
        'session_start_time',
        'session_end_time',
        'price',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'session_start_time' => 'datetime',
        'session_end_time' => 'datetime',
    ];

    /**
     * Sesi ini dimiliki oleh satu User (pasien).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Sesi ini ditangani oleh satu User (psikolog).
     */
    public function psychologist()
    {
        return $this->belongsTo(User::class, 'psychologist_id');
    }

    /**
     * Sesi ini memiliki banyak pesan chat.
     */
    public function chats()
    {
        return $this->hasMany(ChatConsultation::class);
    }

    /**
     * Rekam medis (hasil kuis) yang dibagikan untuk sesi ini.
     */
    public function sharedMedicalRecords()
    {
        return $this->belongsToMany(QuizAttempt::class, 'session_medical_records');
    }

    public function medicalRecords()
    {
        return $this->hasMany(SessionMedicalRecord::class, 'consultation_session_id');
    }

    public function note()
    {
        return $this->hasOne(ConsultationNote::class);
    }
}