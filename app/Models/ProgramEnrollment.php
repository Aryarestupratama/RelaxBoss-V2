<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'user_id',
        'current_day',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Sebuah pendaftaran merujuk ke satu program.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Sebuah pendaftaran dimiliki oleh satu pengguna.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}