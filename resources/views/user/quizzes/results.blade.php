<x-app-layout>
    {{-- Mengatur judul tab browser --}}
    <x-slot name="title">
        Hasil Asesmen: {{ $quiz->name }}
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Asesmen: {{ $quiz->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Header Halaman Hasil --}}
            <div class="text-center mb-12" data-aos="fade-down">
                <div class="w-20 h-20 mx-auto bg-green-100 text-green-600 flex items-center justify-center rounded-full mb-6">
                    <i class="fa-solid fa-square-poll-vertical text-4xl"></i>
                </div>
                <h1 class="mt-4 text-4xl font-extrabold text-gray-900 tracking-tight">
                    Hasil Asesmen Anda
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                    Terima kasih telah meluangkan waktu. Berikut adalah rangkuman dan rekomendasi personal untuk Anda.
                </p>
            </div>

            {{-- Kontainer Hasil Utama --}}
            <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-8 space-y-8 border border-slate-200/50" data-aos="fade-up">
                
                <!-- 1. Rekomendasi Personal dari AI -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <i class="fa-solid fa-sparkles text-blue-500"></i>
                        Insight & Rekomendasi untuk Anda
                    </h2>
                    {{-- [PERBAIKAN] Menambahkan kelas text-justify untuk membuat teks rata kanan-kiri --}}
                    <div class="mt-4 prose prose-blue max-w-none text-gray-600 leading-relaxed text-justify">
                        {!! $attempt->ai_recommendation ? \Illuminate\Support\Str::markdown($attempt->ai_recommendation) : '<p>Rekomendasi sedang dipersiapkan. Silakan muat ulang beberapa saat lagi.</p>' !!}
                    </div>
                </div>

                <!-- 2. Rincian Skor per Sub-Skala -->
                <div>
                    <div class="flex items-center mb-6">
                        <i class="fa-solid fa-chart-simple text-xl text-blue-500 mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Rincian Skor Anda</h2>
                    </div>
                    <div class="space-y-6">
                        @foreach ($results as $subScale => $result)
                            @php
                                $interpretation = $result['interpretation'];
                                $colorClasses = match(strtolower($interpretation)) {
                                    'normal', 'ringan' => 'green',
                                    'sedang' => 'yellow',
                                    'parah', 'sangat parah', 'tinggi' => 'red',
                                    default => 'gray',
                                };
                                $percentage = ($result['score'] / $result['max_score']) * 100;
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
                                       <span class="text-xl font-bold text-gray-900">{{ $result['score'] }} <span class="text-sm font-normal text-gray-500">/ {{ $result['max_score'] }}</span></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{$colorClasses}}-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Aksi Rekam Medis -->
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fa-solid fa-book-medical text-xl text-blue-500 mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Rekam Medis</h2>
                    </div>
                    <p class="text-gray-600 mb-4">Ringkasan ini dapat Anda simpan untuk catatan pribadi atau dibagikan dengan psikolog Anda saat sesi konsultasi.</p>
                    <div class="p-4 bg-slate-50 border border-dashed rounded-lg text-sm text-slate-700 italic">
                        "{{ $attempt->ai_summary ?? 'Ringkasan belum tersedia.' }}"
                    </div>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('quizzes.result.pdf', $attempt) }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-white text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                            <i class="fa-solid fa-download"></i>
                            Unduh PDF
                        </a>
                    </div>
                </div>

                <!-- Bagian Hotline Bantuan Segera -->
                <div class="p-5 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-phone-volume h-6 w-6 text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-base font-semibold text-green-800">Merasa perlu berbicara lebih lanjut?</h3>
                            <p class="text-sm text-green-700 mt-1">
                                Tim kami siap membantu Anda menemukan psikolog yang tepat berdasarkan hasil ini. Hubungi hotline kami untuk mendapatkan rekomendasi personal.
                            </p>
                            <div class="mt-4">
                                @php
                                    $userName = Auth::user()->name;
                                    $quizName = $attempt->quiz->name;
                                    
                                    $aiSummary = $attempt->ai_summary ?? 'Ringkasan belum tersedia.';

                                    $formattedResults = [];
                                    if (is_array($attempt->results)) {
                                        foreach ($attempt->results as $subScale => $result) {
                                            $formattedResults[] = '- ' . ucfirst($subScale) . ': ' . ($result['interpretation'] ?? 'N/A');
                                        }
                                    }
                                    $resultString = implode("\n", $formattedResults);

                                    $message = "Halo RelaxBoss, saya {$userName}.\n\nSaya baru saja menyelesaikan asesmen '{$quizName}'.\n\nBerikut adalah ringkasan AI dari hasil saya:\n\"{$aiSummary}\"\n\nDetail skor:\n{$resultString}\n\nBerdasarkan hasil ini, bisakah Anda membantu merekomendasikan psikolog yang cocok untuk saya? Terima kasih.";
                                    $whatsappUrl = 'https://wa.me/6289604219915?text=' . urlencode($message);
                                @endphp
                                <a href="{{ $whatsappUrl }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition shadow-md">
                                    <i class="fab fa-whatsapp"></i>
                                    Hubungi Hotline via WhatsApp
                                </a>
                            </div>
                        </div>
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

