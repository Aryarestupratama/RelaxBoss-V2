<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Admin User',
            'full_name' => 'Administrator Utama',
            'email' => 'admin@relaxboss.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Psikolog User',
            'full_name' => 'Dr. Psikologi',
            'email' => 'psikolog@relaxboss.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PSIKOLOG,
        ]);

        User::factory()->create([
            'name' => 'Biasa User',
            'full_name' => 'Pengguna Biasa',
            'email' => 'user@relaxboss.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
        ]);
    }
}