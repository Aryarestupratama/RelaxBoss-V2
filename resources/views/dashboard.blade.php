<x-app-layout>
    {{-- Data Dummy - Ganti dengan data asli dari controller Anda --}}
    @php
        $lastAssessment = [
            'level' => 'Rendah', // Bisa 'Rendah', 'Sedang', 'Tinggi'
            'date' => '20 Agustus 2025',
        ];
        $stats = [
            'assessments_taken' => 5,
            'chatbot_sessions' => 12,
        ];

        // Logika untuk menentukan warna berdasarkan level stres
        $stressColor = match($lastAssessment['level']) {
            'Rendah' => 'text-green-600 bg-green-100',
            'Sedang' => 'text-yellow-600 bg-yellow-100',
            'Tinggi' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- 1. Header Sambutan & Status Cepat -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 md:p-8" data-aos="fade-down">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Selamat Datang Kembali, {{ Auth::user()->name }}!</h1>
                        <p class="mt-1 text-gray-500">Siap untuk hari yang lebih tenang dan produktif?</p>
                    </div>
                   
                </div>
            </div>

            <!-- 2. Kartu Aksi Utama -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Kartu Chatbot -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col items-start" data-aos="fade-right">
                    <div class="bg-blue-100 text-[#007BFF] p-3 rounded-lg mb-4">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 013-3h.008c1.657 0 3 1.343 3 3v.375m-6.75 12.375a3 3 0 01-3-3V12m3 3.75a3 3 0 003-3V12m-9 3.75h12.75" /></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Butuh Teman Bicara?</h2>
                    <p class="text-gray-500 mt-1 mb-4 flex-grow">AI RelaxMate siap mendengarkan dan memberikan panduan kapan pun Anda butuhkan, tanpa penilaian.</p>
                    <a href="" class="w-full text-center bg-[#007BFF] hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                        Mulai Percakapan
                    </a>
                </div>

                <!-- Kartu Asesmen Stres -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col items-start" data-aos="fade-left">
                     <div class="bg-green-100 text-green-600 p-3 rounded-lg mb-4">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Ukur Kesejahteraan Anda</h2>
                    <p class="text-gray-500 mt-1 mb-4 flex-grow">Ambil asesmen rutin untuk memahami tingkat stres dan melihat perkembangan positif Anda dari waktu ke waktu.</p>
                    <div class="w-full flex flex-col sm:flex-row gap-3">
                        <a href="" class="w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">Ambil Asesmen Baru</a>
                        <a href="" class="w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-4 rounded-lg transition-colors">Lihat Riwayat</a>
                    </div>
                </div>
            </div>

            <!-- 3. Jembatan ke Komunitas -->
            <div class="bg-gradient-to-r from-[#007BFF] to-cyan-500 text-white shadow-lg sm:rounded-lg p-8 text-center" data-aos="fade-up">
                <h2 class="text-3xl font-bold">Anda Tidak Sendirian</h2>
                <p class="mt-2 max-w-2xl mx-auto opacity-90">Bergabunglah dengan komunitas RelaxBoss untuk berbagi pengalaman, mendapatkan dukungan, dan tumbuh bersama para profesional lainnya di lingkungan yang aman.</p>
                <a href="" class="mt-6 inline-block bg-white text-[#007BFF] font-bold py-3 px-8 rounded-full transition transform hover:scale-105">
                    Masuk ke Forum Komunitas
                </a>
            </div>

            <!-- 4. Riwayat & Progres -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 md:p-8" data-aos="fade-up">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Perkembangan Anda</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <!-- Statistik 1 -->
                    <div class="bg-slate-50 p-4 rounded-lg">
                        <p class="text-3xl font-bold text-[#007BFF]">{{ $stats['assessments_taken'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">Total Asesmen Diambil</p>
                    </div>
                    <!-- Statistik 2 -->
                    <div class="bg-slate-50 p-4 rounded-lg">
                        <p class="text-3xl font-bold text-[#007BFF]">{{ $stats['chatbot_sessions'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">Sesi dengan AI RelaxMate</p>
                    </div>
                    <!-- Statistik 3 - Placeholder -->
                    <div class="bg-slate-50 p-4 rounded-lg">
                        <p class="text-3xl font-bold text-[#007BFF]">12</p>
                        <p class="text-sm text-gray-500 mt-1">Tugas Selesai</p>
                    </div>
                </div>
                <!-- Placeholder untuk Grafik -->
                <div class="mt-6 text-center text-gray-400 border-2 border-dashed rounded-lg p-8">
                    <p>Grafik perkembangan skor asesmen akan ditampilkan di sini.</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
