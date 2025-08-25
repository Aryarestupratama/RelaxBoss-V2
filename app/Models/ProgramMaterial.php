<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'day_number',
        'title',
        'content',
    ];

    /**
     * Sebuah materi dimiliki oleh satu program.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
