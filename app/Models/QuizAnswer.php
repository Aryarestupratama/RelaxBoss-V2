<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'value',
    ];

    /**
     * Sebuah jawaban dimiliki oleh satu percobaan kuis.
     */
    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    /**
     * Sebuah jawaban merujuk pada satu pertanyaan.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
