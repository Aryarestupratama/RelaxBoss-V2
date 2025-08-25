<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image',
        'duration_days',
        'mentor_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke User yang menjadi pembimbing (mentor).
     */
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Sebuah program memiliki banyak materi harian.
     */
    public function materials()
    {
        return $this->hasMany(ProgramMaterial::class);
    }

    /**
     * Sebuah program memiliki banyak pendaftar (peserta).
     */
    public function enrollments()
    {
        return $this->hasMany(ProgramEnrollment::class);
    }

    /**
     * Relasi many-to-many ke User yang mendaftar.
     */
    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'program_enrollments');
    }
}