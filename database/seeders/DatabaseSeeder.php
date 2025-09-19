<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            PsychologistSeeder::class,
            BurnoutScaleSeeder::class,
            Dass21Seeder::class,
            ProgramSeeder::class,
        ]);
    }
}
