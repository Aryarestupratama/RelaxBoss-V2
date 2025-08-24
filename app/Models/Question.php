<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'text',
        'sub_scale',
        'is_reversed',
    ];

    protected $casts = [
        'is_reversed' => 'boolean',
    ];

    /**
     * Sebuah pertanyaan dimiliki oleh satu kuis.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}