<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PomodoroSession extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     * @var array
     */
    protected $fillable = [
        'user_id',
        'todo_id',
        'type',
        'start_time',
        'end_time',
        'duration_minutes',
        'status',
        'remaining_seconds', // <-- Tambahkan ini
    ];

    /**
     * Tipe data asli untuk atribut.
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ]; // [cite: 240, 241]

    /**
     * Relasi ke User yang menjalankan sesi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Tugas yang dikerjakan (jika ada).
     */
    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }
}