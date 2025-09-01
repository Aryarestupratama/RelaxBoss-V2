<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_session_id',
        'notes',
    ];

    public function consultationSession(): BelongsTo
    {
        return $this->belongsTo(ConsultationSession::class);
    }
}



