<?php

namespace App\Models;

use App\Enums\SenderType; // Import Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AiConsultationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'todo_id',
        'sender_type',
        'message_text',
    ];

    protected $casts = [
        // Menggunakan Enum
        'sender_type' => SenderType::class,
    ];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function action(): HasOne
    {
        return $this->hasOne(AiAction::class);
    }
}