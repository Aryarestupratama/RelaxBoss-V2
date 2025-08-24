<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'results',
        'user_context',
        'ai_recommendation',
        'ai_summary',
    ];

    /**
     * Otomatis mengubah kolom 'results' dari JSON ke array dan sebaliknya.
     */
    protected $casts = [
        'results' => 'array',
    ];

    /**
     * Sebuah percobaan kuis dimiliki oleh satu kuis.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Sebuah percobaan kuis dimiliki oleh satu pengguna.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sebuah percobaan kuis memiliki banyak jawaban.
     */
    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }
}