<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskFile extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     * @var array
     */
    protected $fillable = [
        'todo_id',
        'file_category', // Tambahkan ini
        'file_type',
        'file_path',
        'file_name',
    ];

    /**
     * Relasi ke Tugas tempat file dilampirkan.
     */
    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }
}