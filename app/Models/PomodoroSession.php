<?php

namespace App\Models;

use App\Enums\PomodoroType;
use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PomodoroSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'todo_id',
        'type', // <-- DIKEMBALIKAN, sangat penting
        'status',
        'start_time',
        'end_time',
        'duration_minutes', // <-- Penamaan yang lebih jelas
        'remaining_seconds', // <-- Penamaan yang lebih jelas
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer',
        'remaining_seconds' => 'integer',
        
        // Menggunakan Enum
        'type' => PomodoroType::class,
        'status' => SessionStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }
}