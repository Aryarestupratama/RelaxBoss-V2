<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProgramPolicy
{
    /**
     * Tentukan apakah pengguna dapat mengelola program.
     * Hanya pembimbing dari program tersebut yang diizinkan.
     */
    public function manage(User $user, Program $program): bool
    {
        return $user->id === $program->mentor_id;
    }
}
