<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsychologistService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'price_per_session',
        'duration_per_session_minutes',
        'is_free',
        'is_active',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Layanan ini dimiliki oleh satu User (psikolog).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
