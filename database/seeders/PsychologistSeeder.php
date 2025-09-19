<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PsychologistProfile;
use App\Models\Specialization;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PsychologistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Buat beberapa spesialisasi umum
        $specializations = collect([
            ['name' => 'Stres & Kecemasan Kerja'],
            ['name' => 'Depresi & Gangguan Mood'],
            ['name' => 'Masalah Hubungan & Keluarga'],
            ['name' => 'Pengembangan Diri & Karir'],
            ['name' => 'Trauma & PTSD'],
            ['name' => 'Gangguan Tidur'],
        ])->map(function ($spec) {
            return Specialization::create([
                'name' => $spec['name'],
                'slug' => Str::slug($spec['name']),
            ]);
        });

        // 2. Buat data psikolog
        $psychologists = [
            [
                'name' => 'psikolog1',
                'full_name' => 'Psikolog 1, M.Psi., Psikolog',
                'email' => 'psikolog1@relaxboss.test',
                'profile' => [
                    'title' => 'M.Psi., Psikolog',
                    'bio' => 'Psikolog 1 adalah seorang psikolog klinis dengan fokus pada Cognitive Behavioral Therapy (CBT). Beliau berpengalaman membantu klien mengatasi stres, kecemasan, dan depresi yang berkaitan dengan tekanan di lingkungan kerja.',
                    'domicile' => 'Jakarta Selatan',
                    'education' => 'S2 Psikologi Klinis, Universitas Indonesia',
                    'practice_location' => 'Klinik Sehat Jiwa, Jakarta',
                    'years_of_experience' => 8,
                ],
                'specialization_indices' => [0, 1], // Stres & Kecemasan, Depresi
                'services' => [
                    ['type' => 'online', 'price_per_session' => 250000, 'duration_per_session_minutes' => 50],
                    ['type' => 'offline', 'price_per_session' => 400000, 'duration_per_session_minutes' => 60],
                ]
            ],
            [
                'name' => 'psikolog2',
                'full_name' => 'Psikolog 2, S.Psi., M.Psi.',
                'email' => 'psikolog2@relaxboss.test',
                'profile' => [
                    'title' => 'M.Psi., Psikolog',
                    'bio' => 'Dengan pendekatan humanistik, Psikolog 2 berfokus membantu individu dan pasangan dalam menavigasi konflik hubungan, meningkatkan komunikasi, dan membangun kembali keintiman.',
                    'domicile' => 'Bandung',
                    'education' => 'S2 Psikologi Profesi, Universitas Padjadjaran',
                    'practice_location' => 'Praktik Pribadi, Bandung',
                    'years_of_experience' => 12,
                ],
                'specialization_indices' => [2, 3], // Hubungan & Keluarga, Pengembangan Diri
                'services' => [
                    ['type' => 'online', 'price_per_session' => 300000, 'duration_per_session_minutes' => 60],
                ]
            ],
            [
                'name' => 'psikolog3',
                'full_name' => 'Psikolog 3, M.Psi., Psikolog',
                'email' => 'psikolog3@relaxboss.test',
                'profile' => [
                    'title' => 'M.Psi., Psikolog',
                    'bio' => 'Psikolog 3 memiliki spesialisasi dalam penanganan trauma menggunakan metode EMDR. Ia berdedikasi untuk membantu klien memproses pengalaman sulit dan menemukan jalan menuju pemulihan.',
                    'domicile' => 'Surabaya',
                    'education' => 'S2 Psikologi Klinis, Universitas Airlangga',
                    'practice_location' => 'RS Harapan Kita, Surabaya',
                    'years_of_experience' => 6,
                ],
                'specialization_indices' => [4, 1, 5], // Trauma, Depresi, Gangguan Tidur
                'services' => [
                    ['type' => 'online', 'price_per_session' => 275000, 'duration_per_session_minutes' => 50],
                ]
            ],
            [
                'name' => 'psikolog4',
                'full_name' => 'Psikolog 4, M.Psi.',
                'email' => 'psikolog4@relaxboss.test',
                'profile' => [
                    'title' => 'M.Psi., Psikolog',
                    'bio' => 'Psikolog 4 adalah seorang career coach dan psikolog yang membantu para profesional muda dalam menemukan passion, mengatasi burnout, dan merancang jalur karir yang memuaskan.',
                    'domicile' => 'Jakarta Pusat',
                    'education' => 'S2 Psikologi Industri & Organisasi, Universitas Gadjah Mada',
                    'practice_location' => 'Online Consultation',
                    'years_of_experience' => 10,
                ],
                'specialization_indices' => [3, 0], // Pengembangan Diri & Karir, Stres Kerja
                'services' => [
                    ['type' => 'online', 'price_per_session' => 350000, 'duration_per_session_minutes' => 50],
                ]
            ],
             [
                'name' => 'psikolog5',
                'full_name' => 'Psikolog 5, M.Psi.',
                'email' => 'psikolog5@relaxboss.test',
                'profile' => [
                    'title' => 'M.Psi., Psikolog',
                    'bio' => 'Psikolog 5 berfokus pada kesehatan mental remaja dan dewasa muda. Ia menggunakan pendekatan yang suportif dan dinamis untuk mengatasi isu-isu seperti kecemasan sosial, depresi, dan masalah identitas diri.',
                    'domicile' => 'Yogyakarta',
                    'education' => 'S2 Psikologi Klinis, Universitas Gadjah Mada',
                    'practice_location' => 'Pusat Konseling Mahasiswa UGM',
                    'years_of_experience' => 5,
                ],
                'specialization_indices' => [1, 2, 3], // Depresi, Hubungan, Pengembangan Diri
                'services' => [
                    ['type' => 'online', 'price_per_session' => 225000, 'duration_per_session_minutes' => 50],
                ]
            ],
        ];

        foreach ($psychologists as $psychologistData) {
            // 3. Buat user dengan role 'psikolog'
            $user = User::create([
                'name' => $psychologistData['name'],
                'full_name' => $psychologistData['full_name'],
                'email' => $psychologistData['email'],
                'password' => Hash::make('1234567'), // Password default untuk semua
                'role' => 'psikolog',
            ]);

            // 4. Buat profil psikolog yang terhubung
            $user->psychologistProfile()->create($psychologistData['profile']);

            // 5. Hubungkan psikolog dengan spesialisasi
            $specIds = collect($psychologistData['specialization_indices'])->map(function ($index) use ($specializations) {
                return $specializations[$index]->id;
            });
            $user->specializations()->attach($specIds);

            // 6. Tambahkan layanan yang disediakan
            foreach ($psychologistData['services'] as $service) {
                $user->services()->create($service);
            }
        }
    }
}