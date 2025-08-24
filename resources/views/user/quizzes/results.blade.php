<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Asesmen: {{ $quiz->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Header Halaman Hasil --}}
            <div class="text-center mb-12" data-aos="fade-down">
                <svg class="mx-auto h-16 w-16 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z" />
                </svg>
                <h1 class="mt-4 text-4xl font-extrabold text-gray-900 tracking-tight">
                    Hasil Asesmen Anda
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                    Terima kasih telah meluangkan waktu. Berikut adalah rangkuman dan rekomendasi personal untuk Anda.
                </p>
            </div>

            {{-- Kontainer Hasil Utama --}}
            <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-8 space-y-8" data-aos="fade-up">
                
                <!-- 1. Rekomendasi Personal dari AI -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <svg class="w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        Rekomendasi untuk Anda
                    </h2>
                    <div class="mt-4 prose prose-blue max-w-none text-gray-600 leading-relaxed">
                        {{-- Tampilkan rekomendasi dari kolom 'ai_recommendation' --}}
                        <p>{{ $attempt->ai_recommendation ?? 'Rekomendasi sedang dipersiapkan. Silakan muat ulang beberapa saat lagi.' }}</p>
                    </div>
                </div>

                <hr>

                <!-- 2. Rincian Skor per Sub-Skala -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Rincian Skor Anda</h2>
                    <div class="space-y-6">
                        @foreach ($results as $subScale => $result)
                            @php
                                // Logika warna tetap sama, sangat bagus!
                                $interpretation = $result['interpretation'];
                                $colorClasses = match(strtolower($interpretation)) {
                                    'normal', 'ringan' => 'green',
                                    'sedang' => 'yellow',
                                    'parah', 'sangat parah', 'tinggi' => 'red',
                                    default => 'gray',
                                };
                            @endphp
                            <div class="p-4 border-l-4 border-{{$colorClasses}}-500 bg-{{$colorClasses}}-50 rounded-r-lg">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ ucfirst($subScale) }}</h3>
                                    <span class="mt-1 sm:mt-0 px-3 py-1 text-sm font-semibold rounded-full bg-{{$colorClasses}}-100 text-{{$colorClasses}}-800">
                                        {{ $result['interpretation'] }}
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <div class="flex justify-between items-baseline mb-1">
                                         <span class="text-sm font-medium text-gray-600">Skor Anda</span>
                                         <span class="text-xl font-bold text-gray-900">{{ $result['score'] }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        {{-- Logika progress bar Anda sudah bagus --}}
                                        <div class="bg-{{$colorClasses}}-500 h-2 rounded-full" style="width: {{-- Logika persentase Anda di sini --}}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr>

                <!-- 3. Aksi Rekam Medis -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Rekam Medis</h2>
                    <p class="text-gray-600 mb-4">Ringkasan ini dapat Anda simpan atau bagikan dengan psikolog Anda untuk membantu sesi konsultasi.</p>
                    <div class="p-4 bg-slate-50 border border-dashed rounded-lg text-sm text-slate-700 italic">
                        "{{ $attempt->ai_summary ?? 'Ringkasan belum tersedia.' }}"
                    </div>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <button type="button" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-white text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Unduh PDF
                        </button>
                        <button type="button" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-white text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 4.186m0-4.186c.524 1.263 1.232 2.423 2.083 3.512a2.25 2.25 0 003.414 0c.851-1.09 1.56-2.249 2.083-3.512a2.25 2.25 0 000-4.186c-.524-1.263-1.232-2.423-2.083-3.512a2.25 2.25 0 00-3.414 0c-.851 1.09-1.56 2.249-2.083 3.512z" /></svg>
                            Kirim ke Psikolog
                        </button>
                    </div>
                </div>

            </div>

            {{-- Disclaimer Penting --}}
            <div class="mt-8 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 rounded-r-lg">
                <p class="text-sm">
                    <strong>Disclaimer:</strong> Hasil asesmen ini bukan merupakan diagnosis medis. Jika Anda merasa khawatir dengan kondisi Anda, sangat disarankan untuk berkonsultasi dengan profesional.
                </p>
            </div>

            {{-- Tombol Aksi Navigasi --}}
            <div class="mt-12 flex flex-col sm:flex-row justify-center items-center gap-4">
                {{-- [PERBAIKAN KRITIS] Mengganti nama route --}}
                <a href="{{ route('dashboard') }}" class="w-full sm:w-auto text-center px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                    Kembali ke Dashboard
                </a>
                <a href="{{ route('quizzes.index') }}" class="w-full sm:w-auto text-center px-8 py-3 bg-white text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                    Ambil Asesmen Lain
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
