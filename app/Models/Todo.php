<?php

namespace App\Models;

// Import Enum yang akan dibuat
use App\Enums\TodoStatus;
use App\Enums\TodoPriority;
use App\Enums\EisenhowerQuadrant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Todo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'parent_task_id',
        'title',
        'notes',
        'status',
        'priority',
        'eisenhower_quadrant',
        'due_date',
        'completed_at',
        'pomodoro_custom_duration', // <-- [DIUBAH] Sesuai dengan database
        'pomodoro_cycles_completed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'user_id' => 'integer',
        'project_id' => 'integer',
        'parent_task_id' => 'integer',
        'pomodoro_custom_duration' => 'integer', // <-- [DIUBAH] Sesuai dengan database
        'pomodoro_cycles_completed' => 'integer',
        
        'status' => TodoStatus::class,
        'priority' => TodoPriority::class,
        'eisenhower_quadrant' => EisenhowerQuadrant::class,
    ];

    /**
     * Get the user that owns the todo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project that the todo belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the parent task for this sub-task.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Todo::class, 'parent_task_id');
    }

    /**
     * Get the sub-tasks for this task.
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Todo::class, 'parent_task_id');
    }

    /**
     * Get the pomodoro sessions for the todo.
     */
    public function pomodoroSessions(): HasMany
    {
        return $this->hasMany(PomodoroSession::class);
    }

    /**
     * Get the files for the todo.
     */
    public function files(): HasMany
    {
        return $this->hasMany(TaskFile::class);
    }

    /**
     * Get the AI consultation messages for the todo.
     */
    public function aiConsultationMessages(): HasMany
    {
        return $this->hasMany(AiConsultationMessage::class)->orderBy('created_at');
    }
}
