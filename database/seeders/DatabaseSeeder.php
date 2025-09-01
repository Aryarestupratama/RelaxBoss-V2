<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // public function run(): void
    // {
    //     User::factory()->create([
    //         'name' => 'Admin User',
    //         'full_name' => 'Administrator Utama', // <-- Tambahkan
    //         'email' => 'admin@relaxboss.com',
    //         'password' => Hash::make('password'),
    //         'role' => UserRole::ADMIN,
    //     ]);

    //     User::factory()->create([
    //         'name' => 'Psikolog User',
    //         'full_name' => 'Dr. Psikologi', // <-- Tambahkan
    //         'email' => 'psikolog@relaxboss.com',
    //         'password' => Hash::make('password'),
    //         'role' => UserRole::PSIKOLOG,
    //     ]);

    //     User::factory()->create([
    //         'name' => 'Biasa User',
    //         'full_name' => 'Pengguna Biasa', // <-- Tambahkan
    //         'email' => 'user@relaxboss.com',
    //         'password' => Hash::make('password'),
    //         'role' => UserRole::USER,
    //     ]);
    // }
    public function run()
    {
        $this->call([
            PsychologistSeeder::class,
        ]);
    }
}