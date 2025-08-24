<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoringRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'sub_scale',
        'min_score',
        'max_score',
        'interpretation',
    ];

    /**
     * Sebuah aturan penilaian dimiliki oleh satu kuis.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
