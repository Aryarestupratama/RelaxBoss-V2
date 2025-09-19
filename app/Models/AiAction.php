<?php

namespace App\Models;

use App\Enums\AiActionStatus; // Import Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_consultation_message_id',
        'function_name',
        'arguments',
        'status',
        'response',
    ];

    protected $casts = [
        'arguments' => 'array',
        
        // Menggunakan Enum
        'status' => AiActionStatus::class,
    ];

    public function consultationMessage(): BelongsTo
    {
        return $this->belongsTo(AiConsultationMessage::class, 'ai_consultation_message_id');
    }
}