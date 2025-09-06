<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Todo extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     * @var array
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'parent_task_id',
        'task',
        'notes',
        'status',
        'priority',
        'eisenhower_quadrant',
        'due_date',
        'completed_at',
        'pomodoro_custom_duration',
        'pomodoro_cycles_completed',
    ];

    /**
     * Tipe data asli untuk atribut.
     * @var array
     */
    protected $casts = [
        // Casts yang sudah ada (benar)
        'due_date' => 'datetime',
        'completed_at' => 'datetime',

        // TAMBAHKAN INI UNTUK MEMASTIKAN TIPE DATA ID & ANGKA
        'user_id' => 'integer',
        'project_id' => 'integer',
        'parent_task_id' => 'integer',
        'pomodoro_custom_duration' => 'integer',
        'pomodoro_cycles_completed' => 'integer',
    ];

    /**
     * Relasi ke User yang memiliki tugas.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Project (jika ada).
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relasi ke tugas induk (jika ini adalah sub-tugas).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Todo::class, 'parent_task_id');
    }

    /**
     * Relasi ke semua sub-tugas dari tugas ini.
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Todo::class, 'parent_task_id');
    }

    /**
     * Relasi ke sesi Pomodoro yang terkait.
     */
    public function pomodoroSessions(): HasMany
    {
        return $this->hasMany(PomodoroSession::class);
    }

    /**
     * Relasi ke file-file yang dilampirkan.
     */
    public function files(): HasMany
    {
        return $this->hasMany(TaskFile::class);
    }
}