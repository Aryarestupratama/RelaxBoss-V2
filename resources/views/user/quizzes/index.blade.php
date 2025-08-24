{{-- Menggunakan layout komponen standar Breeze untuk konsistensi --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Asesmen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ search: '' }">

                {{-- Header Halaman dan Input Pencarian --}}
                <div class="text-center mb-12" data-aos="fade-down">
                    <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight sm:text-5xl">
                        Pahami Diri Anda
                    </h1>
                    <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                        Pilih salah satu asesmen di bawah ini untuk memulai perjalanan Anda menuju kesejahteraan.
                    </p>
                    <div class="mt-8 max-w-md mx-auto">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input x-model="search" type="text" placeholder="Cari nama asesmen..." class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>
                </div>

                {{-- Grid untuk Kartu Kuis --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse ($quizzes as $quiz)
                        <div x-show="search === '' || '{{ strtolower($quiz->name) }}'.includes(search.toLowerCase())" x-transition>
                            
                            {{-- Kartu Kuis --}}
                            <div class="bg-white shadow-lg rounded-2xl overflow-hidden h-full flex flex-col border-2 transition-all duration-300 {{ $quiz->attempted_today ? 'border-green-400' : 'border-transparent hover:border-blue-400' }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="p-6 flex-grow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h2 class="text-xl font-bold text-gray-900">{{ $quiz->name }}</h2>
                                        @if($quiz->attempted_today)
                                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-100">
                                                Selesai
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-600 mb-6 text-sm leading-relaxed">{{ Str::limit($quiz->description, 140) }}</p>
                                </div>
                                
                                <div class="px-6 pb-6 bg-white mt-auto">
                                    <div class="flex items-center text-sm text-gray-500 mb-4">
                                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <span>{{ $quiz->questions_count }} Pertanyaan</span>
                                    </div>

                                    {{-- [PERBAIKAN KRITIS] Mengganti nama route agar sesuai dengan routes/web.php --}}
                                    @if ($quiz->attempted_today)
                                        <div class="space-y-3">
                                            <a href="{{ route('quizzes.result', $quiz->latest_attempt_id_today) }}" class="block w-full text-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
                                                Lihat Hasil
                                            </a>
                                            <a href="{{ route('quizzes.show', $quiz) }}" class="block w-full text-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">
                                                Ulangi Asesmen
                                            </a>
                                        </div>
                                    @else
                                        <a href="{{ route('quizzes.show', $quiz) }}" class="block w-full text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                                            Mulai Asesmen
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- [IMPROVISASI] Tampilan "empty state" yang lebih menarik --}}
                        <div class="md:col-span-2 lg:col-span-3 text-center py-16 px-6 bg-white rounded-lg shadow-md">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Belum Ada Asesmen</h3>
                            <p class="mt-1 text-sm text-gray-500">Saat ini belum ada asesmen yang tersedia. Silakan cek kembali nanti.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
