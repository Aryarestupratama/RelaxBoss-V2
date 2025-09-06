<x-app-layout>
    {{-- Mengatur judul halaman agar dinamis --}}
    <x-slot name="title">
        {{ __('Dashboard') }}
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">
                
               <!-- 1. Header Sambutan & Status Cepat -->
                <div class="bg-gradient-to-br from-white to-slate-50 border border-slate-200/50 shadow-lg sm:rounded-2xl p-6 md:p-8" data-aos="fade-down">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            @php
                                $hour = now()->format('H');
                                $isNight = $hour >= 18 || $hour < 6; // 18.00 - 05.59 dianggap malam
                            @endphp

                            <div class="flex items-center gap-4">
                                {{-- Gambar RelaxMate Dinamis --}}
                                <div class="hidden sm:block w-32 h-32 md:w-48 md:h-48 lg:w-56 lg:h-56 flex-shrink-0">
                                    @if($isNight)
                                        <img src="{{ asset('storage/components/relaxmate-night.png') }}" alt="RelaxMate Night" class="w-full h-full object-contain">
                                    @else
                                        <img src="{{ asset('storage/components/relaxmate-morning.png') }}" alt="RelaxMate Morning" class="w-full h-full object-contain">
                                    @endif
                                </div>

                                {{-- Teks Sambutan --}}
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-800">
                                        Selamat {{ $isNight ? 'Malam' : 'Datang' }}, {{ $user->name }}!
                                    </h1>
                                    <p class="mt-1 text-gray-500">
                                        {{ $isNight ? 'Waktunya istirahat dengan tenang 🌙' : 'Siap untuk hari yang lebih tenang dan produktif? ☀️' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @if($latestAttempt && isset($latestAttempt->results))
                            @php
                                $dominantStress = collect($latestAttempt->results)->sortByDesc('score')->first();
                                $stressLevel = $dominantStress['interpretation'] ?? 'Belum Diketahui';
                                $stressColor = match(strtolower($stressLevel)) {
                                    'normal', 'ringan' => 'text-green-700 bg-green-100 border-green-200',
                                    'sedang' => 'text-yellow-700 bg-yellow-100 border-yellow-200',
                                    'parah', 'sangat parah', 'tinggi' => 'text-red-700 bg-red-100 border-red-200',
                                    default => 'text-gray-700 bg-gray-100 border-gray-200',
                                };
                            @endphp
                            <div class="text-left md:text-right flex-shrink-0 w-full md:w-auto">
                                <p class="text-sm text-gray-500 mb-1">Tingkat Stres Terakhir:</p>
                                <span class="px-3 py-1.5 text-sm font-bold rounded-full border {{ $stressColor }}">
                                    {{ $stressLevel }}
                                </span>
                                <p class="text-xs text-gray-400 mt-2">Diambil pada {{ $latestAttempt->created_at->format('d F Y') }}</p>
                            </div>
                        @else
                            <div class="text-left md:text-right flex-shrink-0 w-full md:w-auto">
                                <a href="{{ route('quizzes.index') }}" class="inline-block bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold px-6 py-3 rounded-full shadow-lg hover:scale-105 transition transform duration-300">
                                    Ambil Asesmen Pertama &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notifikasi Sesi Konsultasi Akan Datang -->
                @if($upcomingSession)
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-lg sm:rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4" data-aos="fade-up">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 bg-white/20 w-14 h-14 flex items-center justify-center rounded-lg">
                            <i class="fa-solid fa-calendar-check text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Sesi Anda Berikutnya</h3>
                            <p class="text-sm opacity-90">
                                dengan <strong>{{ $upcomingSession->psychologist->name }}</strong> pada 
                                <strong>{{ \Carbon\Carbon::parse($upcomingSession->session_start_time)->isoFormat('dddd, D MMM YYYY, HH:mm') }} WIB</strong>
                            </p>
                        </div>
                    </div>
                    
                    <div x-data="{
                        sessionStartTime: new Date('{{ $upcomingSession->session_start_time }}'),
                        sessionEndTime: new Date('{{ $upcomingSession->session_end_time }}'),
                        isSessionToday() {
                            const now = new Date();
                            return this.sessionStartTime.getFullYear() === now.getFullYear() &&
                                   this.sessionStartTime.getMonth() === now.getMonth() &&
                                   this.sessionStartTime.getDate() === now.getDate();
                        },
                        enterSession() {
                            const now = new Date();
                            const minutesUntilStart = (this.sessionStartTime - now) / 1000 / 60;

                            if (now > this.sessionEndTime) {
                                Swal.fire({ icon: 'error', title: 'Sesi Telah Berakhir', text: 'Sesi konsultasi ini sudah selesai.', confirmButtonColor: '#007BFF' });
                            } else if (minutesUntilStart > 15) {
                                Swal.fire({ icon: 'warning', title: 'Sesi Belum Dimulai', text: `Anda baru bisa masuk ke ruang sesi 15 menit sebelum jadwal dimulai.`, confirmButtonColor: '#007BFF' });
                            } else {
                                window.location.href = '{{ route('consultation.show', $upcomingSession) }}';
                            }
                        }
                    }">
                        <button @click="enterSession()" 
                                :disabled="!isSessionToday()"
                                class="w-full sm:w-auto flex-shrink-0 text-center bg-white text-green-600 font-bold py-2 px-6 rounded-full transition transform hover:scale-105 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed disabled:scale-100">
                            Masuk Ruang Sesi
                        </button>
                    </div>

                </div>
                @endif

                <!-- 2. Kartu Aksi Utama -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Kartu Chatbot -->
                    <div class="bg-white border border-slate-200/50 shadow-lg sm:rounded-2xl p-6 flex flex-col items-start" data-aos="fade-right">
                        <div class="bg-blue-100 text-blue-600 w-14 h-14 flex items-center justify-center rounded-lg mb-4">
                            <i class="fa-solid fa-headset text-2xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Butuh Teman Bicara?</h2>
                        <p class="text-gray-500 mt-1 mb-4 flex-grow">AI RelaxMate siap mendengarkan dan memberikan panduan kapan pun Anda butuhkan, tanpa penilaian.</p>
                        <a href="{{ route('relaxmate.index') }}" 
                        class="w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            Mulai Percakapan
                        </a>
                    </div>

                    <!-- Kartu Asesmen Stres -->
                    <div class="bg-white border border-slate-200/50 shadow-lg sm:rounded-2xl p-6 flex flex-col items-start" data-aos="fade-left">
                         <div class="bg-green-100 text-green-600 w-14 h-14 flex items-center justify-center rounded-lg mb-4">
                            <i class="fa-solid fa-circle-check text-2xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Ukur Kesejahteraan Anda</h2>
                        <p class="text-gray-500 mt-1 mb-4 flex-grow">Ambil asesmen rutin untuk memahami tingkat stres dan melihat perkembangan positif Anda dari waktu ke waktu.</p>
                        <div class="w-full flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('quizzes.index') }}" class="w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">Ambil Asesmen Baru</a>
                            <a href="{{ route('quizzes.history') }}" class="w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-4 rounded-lg transition-colors">Lihat Riwayat</a>
                        </div>
                    </div>
                </div>

                <!-- 3. Jembatan ke Komunitas -->
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-lg sm:rounded-2xl p-8 text-center" data-aos="fade-up">
                    <h2 class="text-3xl font-bold">Anda Tidak Sendirian</h2>
                    <p class="mt-2 max-w-2xl mx-auto opacity-90">Bergabunglah dengan komunitas untuk berbagi pengalaman, mendapatkan dukungan, dan tumbuh bersama para profesional lainnya.</p>
                    <a href="{{ route('programs.index') }}" class="mt-6 inline-block bg-white text-blue-600 font-bold py-3 px-8 rounded-full transition transform hover:scale-105">
                        Jelajahi Program Komunitas
                    </a>
                </div>

                <!-- 4. Riwayat & Progres -->
                <div class="bg-white border border-slate-200/50 shadow-lg sm:rounded-2xl p-6 md:p-8" data-aos="fade-up">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Perkembangan Anda</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <!-- Statistik 1 -->
                        <div class="bg-slate-50 border border-slate-200/80 p-4 rounded-lg">
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['assessments_taken'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">Total Asesmen Diambil</p>
                        </div>
                        <!-- Statistik 2 -->
                        <div class="bg-slate-50 border border-slate-200/80 p-4 rounded-lg">
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['programs_joined'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">Program Diikuti</p>
                        </div>
                        <!-- Statistik 3 -->
                        <div class="bg-slate-50 border border-slate-200/80 p-4 rounded-lg">
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['chatbot_sessions'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">Sesi dengan AI RelaxMate</p>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        @if(count($chartData['labels']) > 1)
                            <div class="h-64">
                                <canvas id="stressChart"></canvas>
                            </div>
                        @else
                            <div class="text-center text-gray-400 border-2 border-dashed rounded-lg p-8">
                                <p>Ambil setidaknya dua asesmen untuk melihat grafik perkembangan Anda.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <!-- CDN SweetAlert2 & Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module">
        import Chart from 'https://cdn.jsdelivr.net/npm/chart.js';

        const ctx = document.getElementById('stressChart');
        if (ctx) {
            const chartData = @json($chartData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Skor Stres Tertinggi',
                        data: chartData.data,
                        borderColor: '#007BFF',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#007BFF',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#007BFF',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e5e7eb' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 6,
                            displayColors: false
                        }
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>

