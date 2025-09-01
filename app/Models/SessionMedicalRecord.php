<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionMedicalRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'session_medical_records';

    /**
     * Indicates if the model should be timestamped.
     * Tabel ini tidak memiliki kolom created_at dan updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'consultation_session_id',
        'quiz_attempt_id',
    ];

    /**
     * Mendapatkan data percobaan kuis yang terhubung dengan rekam medis ini.
     */
    public function quizAttempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    /**
     * Mendapatkan data sesi konsultasi yang memiliki rekam medis ini.
     */
    public function consultationSession(): BelongsTo
    {
        return $this->belongsTo(ConsultationSession::class, 'consultation_session_id');
    }
}
