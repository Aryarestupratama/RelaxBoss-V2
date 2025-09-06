<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TodoPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Todo $todo): bool
    {
        // Izinkan update HANYA jika user_id di tugas sama dengan id user yang sedang login
        return $user->id === $todo->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Todo $todo): bool
    {
        // Izinkan hapus HANYA jika user_id di tugas sama dengan id user yang sedang login
        return $user->id === $todo->user_id;
    }

    // ... metode lainnya bisa ditambahkan nanti
}