<x-app-layout>
    {{-- Mengatur judul tab browser --}}
    <x-slot name="title">
        Pengantar: {{ $quiz->name }}
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengantar Asesmen: {{ $quiz->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-200/50" data-aos="fade-up">
                <div class="p-6 sm:p-8">
                    
                    {{-- Header dengan Ikon --}}
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto bg-blue-100 text-blue-600 flex items-center justify-center rounded-full mb-6">
                            <i class="fa-solid fa-file-signature text-4xl"></i>
                        </div>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                            {{ $quiz->name }}
                        </h1>
                        <p class="mt-4 max-w-3xl mx-auto text-lg text-gray-600">
                            {{ $quiz->description }}
                        </p>
                    </div>

                    <hr class="my-8 border-gray-200">

                    {{-- Detail Asesmen --}}
                    <div class="space-y-10">
                        <!-- Aspek yang Diukur -->
                        <div>
                            <div class="flex items-center mb-4">
                                <i class="fa-solid fa-tags text-xl text-blue-500 mr-3"></i>
                                <h2 class="text-xl font-bold text-gray-800">Aspek yang Diukur</h2>
                            </div>
                            <p class="text-gray-600 mb-4">Asesmen ini akan mengukur tingkat stres Anda dalam beberapa area kunci:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($subScales as $scale)
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $scale }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Skala Penilaian -->
                        <div>
                            <div class="flex items-center mb-4">
                                <i class="fa-solid fa-ruler-horizontal text-xl text-blue-500 mr-3"></i>
                                <h2 class="text-xl font-bold text-gray-800">Skala Penilaian</h2>
                            </div>
                            <p class="text-gray-600 mb-4">Untuk setiap pertanyaan, pilih jawaban yang paling mewakili perasaan Anda selama <strong>satu minggu terakhir</strong>:</p>
                            <ul class="space-y-3">
                                @foreach($quiz->likertOptions->sortBy('value') as $option)
                                <li class="flex items-center p-3 bg-slate-50 rounded-lg border border-slate-200">
                                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-blue-600 text-white font-bold rounded-full text-sm">{{ $option->value }}</span>
                                    <span class="ml-4 text-gray-700 font-medium">{{ $option->label }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Cara Kerja Hasil -->
                        <div>
                            <div class="flex items-center mb-4">
                                <i class="fa-solid fa-lightbulb text-xl text-blue-500 mr-3"></i>
                                <h2 class="text-xl font-bold text-gray-800">Bagaimana Hasil Bekerja?</h2>
                            </div>
                            <p class="text-gray-600 leading-relaxed">
                                Setelah Anda menyelesaikan semua pertanyaan, sistem akan menghitung skor Anda untuk setiap aspek. Jika hasilnya menunjukkan tingkat stres sedang atau tinggi, Anda akan diberi kesempatan untuk menceritakan konteksnya agar AI kami dapat memberikan rekomendasi yang lebih personal dan akurat.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="px-6 py-5 bg-slate-50 border-t border-slate-200/80 flex flex-col sm:flex-row-reverse sm:justify-between items-center gap-4">
                    <a href="{{ route('quizzes.show', $quiz) }}" class="inline-block w-full sm:w-auto text-center px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition transform hover:scale-105 duration-300">
                        Saya Mengerti, Mulai Asesmen
                    </a>
                    <a href="{{ route('quizzes.index') }}" class="text-sm text-gray-600 hover:text-gray-900 transition">
                        &larr; Kembali ke daftar asesmen
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

