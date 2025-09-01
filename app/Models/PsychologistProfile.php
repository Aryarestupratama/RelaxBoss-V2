<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsychologistProfile extends Model
{
    use HasFactory;

    /**
     * [PERBAIKAN] Menambahkan semua kolom ke properti $fillable.
     * Ini memberitahu Laravel bahwa kolom-kolom ini aman untuk diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'str_number',
        'sipp_number',
        'bio',
        'domicile',
        'education',
        'practice_location',
        'years_of_experience',
        'intro_template_message',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_available' => 'boolean',
    ];

    /**
     * Profil ini dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
