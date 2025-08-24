<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'score_multiplier',
    ];

    /**
     * Sebuah kuis memiliki banyak pertanyaan.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Sebuah kuis memiliki banyak pilihan jawaban Likert.
     */
    public function likertOptions()
    {
        return $this->hasMany(LikertOption::class);
    }

    /**
     * Sebuah kuis memiliki banyak aturan penilaian.
     */
    public function scoringRules()
    {
        return $this->hasMany(ScoringRule::class);
    }

    /**
     * Sebuah kuis dapat dicoba berkali-kali.
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}