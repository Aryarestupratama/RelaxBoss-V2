<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Mendapatkan semua psikolog yang memiliki spesialisasi ini.
     */
    public function psychologists()
    {
        return $this->belongsToMany(User::class, 'psychologist_specialization');
    }
}
