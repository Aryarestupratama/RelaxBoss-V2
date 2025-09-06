<x-app-layout>
    <x-slot name="title">
        Pusat Asesmen
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- [DIUBAH] Tambahkan variabel baru di x-data untuk filter kategori --}}
            <div x-data="{
                search: '',
                activeSubScale: 'all', // State untuk filter kategori
                quizzes: {{ json_encode($quizzes) }},
                get filteredQuizzes() {
                    return this.quizzes.filter(quiz => {
                        const searchMatch = this.search === '' || quiz.name.toLowerCase().includes(this.search.toLowerCase());
                        const subScaleMatch = this.activeSubScale === 'all' || quiz.sub_scales.includes(this.activeSubScale);
                        return searchMatch && subScaleMatch;
                    });
                }
            }">

                <div class="text-center mb-12" data-aos="fade-down">
                    {{-- Gambar RelaxMate Assessment --}}
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('storage/components/relaxmate-assesment.png') }}" 
                            alt="RelaxMate Assessment" 
                            class="max-w-full h-auto w-[70%] sm:w-[60%] md:w-[50%] lg:w-[35%] xl:w-[30%] object-contain drop-shadow-md">
                    </div>

                    <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight sm:text-5xl">
                        Pahami Diri Anda
                    </h1>
                    <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                        Pilih salah satu asesmen di bawah ini untuk memulai perjalanan Anda menuju kesejahteraan.
                    </p>
                </div>
                
                {{-- [BARU] Filter Kategori (Sub-Skala) --}}
                <div class="mb-10 flex flex-wrap justify-center gap-2" data-aos="fade-up">
                    <button @click="activeSubScale = 'all'"
                            :class="{ 'bg-blue-600 text-white': activeSubScale === 'all', 'bg-white text-gray-700 hover:bg-slate-100': activeSubScale !== 'all' }"
                            class="px-4 py-2 text-sm font-semibold rounded-full border border-gray-200 shadow-sm transition">
                        Semua Kategori
                    </button>
                    @foreach($subScaleCategories as $category)
                    <button @click="activeSubScale = '{{ $category }}'"
                            :class="{ 'bg-blue-600 text-white': activeSubScale === '{{ $category }}', 'bg-white text-gray-700 hover:bg-slate-100': activeSubScale !== '{{ $category }}' }"
                            class="px-4 py-2 text-sm font-semibold rounded-full border border-gray-200 shadow-sm transition">
                        {{ $category }}
                    </button>
                    @endforeach
                </div>

                {{-- Input Pencarian Nama --}}
                <div class="mb-12 max-w-md mx-auto" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                             <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <input x-model.debounce.300ms="search" type="text" placeholder="Cari nama asesmen..." class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                </div>

                {{-- Grid untuk Kartu Kuis --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {{-- [DIUBAH] Loop sekarang menggunakan filteredQuizzes dari AlpineJS --}}
                    <template x-for="(quiz, index) in filteredQuizzes" :key="quiz.id">
                        <div x-transition class="h-full">
                            <div class="bg-white shadow-xl rounded-2xl overflow-hidden h-full flex flex-col border-2 transition-all duration-300" 
                                 :class="quiz.attempted_today ? 'border-green-400' : 'border-transparent hover:border-blue-400'"
                                 data-aos="fade-up" :data-aos-delay="index * 50">
                                
                                <div class="p-6 flex-grow flex flex-col">
                                    <div class="flex justify-between items-start mb-2">
                                        <h2 class="text-xl font-bold text-gray-900" x-text="quiz.name"></h2>
                                        <span x-show="quiz.attempted_today" class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-100">Selesai</span>
                                    </div>
                                    <p class="text-gray-600 mb-6 text-sm leading-relaxed flex-grow" x-text="quiz.description.substring(0, 140) + (quiz.description.length > 140 ? '...' : '')"></p>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fa-solid fa-list-check text-blue-500 mr-2"></i>
                                        <span x-text="`${quiz.questions_count} Pertanyaan`"></span>
                                    </div>
                                </div>
                                
                                <div class="px-6 pb-6 bg-white mt-auto">
                                    <div x-show="quiz.attempted_today">
                                        <a :href="`/quizzes/result/${quiz.latest_attempt_id_today}`" 
                                        class="block w-full text-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
                                        Lihat Hasil
                                        </a>
                                    </div>
                                    <div x-show="!quiz.attempted_today">
                                        {{-- [PERBAIKAN KRITIS] Menggunakan quiz.slug, bukan quiz.id --}}
                                        <a :href="`/quizzes/${quiz.slug}/introduction`" class="block w-full text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">Mulai Asesmen</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Pesan Jika Tidak Ditemukan --}}
                    <div x-show="filteredQuizzes.length === 0" class="md:col-span-2 lg:col-span-3 text-center py-16 px-6 bg-white rounded-2xl shadow-xl border border-slate-200/50" x-cloak>
                        <div class="w-20 h-20 mx-auto bg-slate-100 text-slate-400 flex items-center justify-center rounded-full mb-6">
                            <i class="fa-solid fa-folder-open text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak Ada Asesmen Ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian atau filter kategori Anda.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>