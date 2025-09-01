<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\ProgramMaterial;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $programs = [
            [
                'name' => '7 Hari Meditasi Mindfulness',
                'description' => 'Program singkat untuk memperkenalkan Anda pada dasar-dasar meditasi mindfulness. Belajar untuk fokus pada saat ini, mengurangi stres, dan menemukan ketenangan dalam kesibukan sehari-hari.',
                'duration_days' => 7,
            ],
            [
                'name' => 'Membangun Kebiasaan Tidur Sehat',
                'description' => 'Dalam 14 hari, program ini akan memandu Anda melalui teknik dan kebiasaan yang terbukti secara ilmiah untuk meningkatkan kualitas tidur, mulai dari rutinitas malam hingga manajemen pikiran.',
                'duration_days' => 14,
            ],
            [
                'name' => 'Mengelola Stres di Tempat Kerja',
                'description' => 'Program 10 hari yang dirancang khusus untuk para profesional. Pelajari cara mengidentifikasi pemicu stres, membangun ketahanan, dan menerapkan strategi praktis untuk menjaga keseimbangan kerja-hidup.',
                'duration_days' => 10,
            ],
            [
                'name' => 'Detoks Digital: Temukan Ketenangan',
                'description' => 'Merasa lelah dengan notifikasi tanpa henti? Program 5 hari ini akan membantu Anda menciptakan hubungan yang lebih sehat dengan teknologi dan merebut kembali fokus serta waktu Anda.',
                'duration_days' => 5,
            ],
            [
                'name' => 'Mengatasi Kecemasan Sosial',
                'description' => 'Sebuah perjalanan 21 hari untuk memahami akar dari kecemasan sosial. Anda akan dibimbing melalui latihan bertahap untuk membangun kepercayaan diri dan merasa lebih nyaman dalam interaksi sosial.',
                'duration_days' => 21,
            ],
        ];

        foreach ($programs as $programData) {
            // 1. Buat Program Utama
            $program = Program::create([
                'name' => $programData['name'],
                'slug' => Str::slug($programData['name']),
                'description' => $programData['description'],
                'duration_days' => $programData['duration_days'],
                'mentor_id' => 2, // Sesuai permintaan
                'is_active' => true,
                'cover_image' => null, // Anda bisa menambahkan path gambar di sini
            ]);

            // 2. Buat Materi Harian untuk setiap program
            for ($day = 1; $day <= $program->duration_days; $day++) {
                ProgramMaterial::create([
                    'program_id' => $program->id,
                    'day_number' => $day,
                    'title' => "Materi Hari ke-{$day}: " . $this->generateMaterialTitle($program->name, $day),
                    'content' => $this->generateMaterialContent($day),
                ]);
            }
        }
    }

    /**
     * Helper untuk membuat judul materi yang relevan (contoh).
     */
    private function generateMaterialTitle(string $programName, int $day): string
    {
        if (Str::contains($programName, 'Meditasi')) {
            return "Latihan Napas & Kesadaran Penuh";
        }
        if (Str::contains($programName, 'Tidur')) {
            return "Menciptakan Rutinitas Malam yang Menenangkan";
        }
        return "Refleksi dan Latihan Harian";
    }

    /**
     * Helper untuk membuat konten materi (contoh dalam format HTML).
     */
    private function generateMaterialContent(int $day): string
    {
        return "<h2>Selamat Datang di Hari ke-{$day}!</h2>
        <p>Hari ini kita akan fokus pada pemahaman yang lebih dalam tentang diri kita. Luangkan waktu sejenak untuk merenungkan topik hari ini.</p>
        <p><strong>Latihan untuk hari ini:</strong> Coba identifikasi tiga hal yang Anda syukuri. Tuliskan dalam jurnal Anda dan rasakan perbedaannya. Latihan sederhana ini dapat membantu mengubah perspektif Anda secara signifikan.</p>
        <p>Ingat, perjalanan ini adalah maraton, bukan sprint. Bersabarlah dengan diri sendiri.</p>";
    }
}