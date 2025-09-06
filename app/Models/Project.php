<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
    ]; // [cite: 203, 204, 205]

    /**
     * Relasi ke User yang memiliki project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke semua tugas di dalam project ini.
     */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }
}