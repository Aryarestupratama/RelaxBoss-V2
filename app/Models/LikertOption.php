<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikertOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'label',
        'value',
    ];

    /**
     * Sebuah pilihan Likert dimiliki oleh satu kuis.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
