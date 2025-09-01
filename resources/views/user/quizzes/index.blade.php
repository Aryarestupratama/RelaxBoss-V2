<x-app-layout>
    {{-- [BARU] Mengatur judul halaman --}}
    <x-slot name="title">
        {{ __('Pusat Asesmen') }}
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
                                {{-- [DIUBAH] Ikon SVG diganti dengan Font Awesome --}}
                                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
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
                            <div class="bg-white shadow-lg border rounded-2xl overflow-hidden h-full flex flex-col transition-all duration-300 {{ $quiz->attempted_today ? 'border-green-400' : 'border-slate-200/50 hover:border-blue-400 hover:shadow-xl' }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
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
                                        {{-- [DIUBAH] Ikon SVG diganti dengan Font Awesome --}}
                                        <i class="fa-solid fa-list-check w-5 h-5 mr-2 text-blue-500"></i>
                                        <span>{{ $quiz->questions_count }} Pertanyaan</span>
                                    </div>

                                    @if ($quiz->attempted_today)
                                        <a href="{{ route('quizzes.result', $quiz->latest_attempt_id_today) }}" class="block w-full text-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
                                            Lihat Hasil
                                        </a>
                                    @else
                                        <a href="{{ route('quizzes.introduction', $quiz) }}" class="block w-full text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                                            Mulai Asesmen
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 lg:col-span-3 text-center py-16 px-6 bg-white rounded-lg shadow-md">
                            {{-- [DIUBAH] Ikon SVG diganti dengan Font Awesome --}}
                            <i class="fa-solid fa-folder-open mx-auto text-4xl text-gray-400"></i>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Asesmen</h3>
                            <p class="mt-1 text-sm text-gray-500">Saat ini belum ada asesmen yang tersedia. Silakan cek kembali nanti.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
