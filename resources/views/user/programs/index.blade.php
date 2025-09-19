<x-app-layout>
    <x-slot name="title">
        Komunitas & Program
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'all' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="text-center mb-12" data-aos="fade-down">
                {{-- Gambar RelaxMate Program --}}
                <div class="mb-6 flex justify-center">
                    <img src="{{ asset('build/assets/relaxmate-program.png') }}" 
                        alt="RelaxMate Program" 
                        class="max-w-full h-auto w-[90%] sm:w-[80%] md:w-[70%] lg:w-[60%] xl:w-[50%] object-contain rounded-xl shadow-md">
                </div>

                <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight sm:text-5xl">
                    Tumbuh Bersama Komunitas
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                    Temukan dukungan dan bimbingan dalam program terstruktur yang dirancang untuk membantu Anda mencapai tujuan kesejahteraan.
                </p>
            </div>
   
            <!-- Tabs -->
            <div class="mb-10">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex justify-center space-x-6" aria-label="Tabs">
                        <button 
                            @click="activeTab = 'all'" 
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'all', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'all' }" 
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Semua Program
                        </button>
                        <button 
                            @click="activeTab = 'enrolled'" 
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'enrolled', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'enrolled' }" 
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Program Saya
                        </button>
                        <button 
                            @click="activeTab = 'available'" 
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'available', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'available' }" 
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Program Tersedia
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $hasAll = count($programs) > 0;
                    $hasEnrolled = $programs->whereIn('id', $enrolledProgramIds)->count() > 0;
                    $hasAvailable = $programs->whereNotIn('id', $enrolledProgramIds)->count() > 0;
                @endphp

                @forelse ($programs as $program)
                    @php
                        $isEnrolled = in_array($program->id, $enrolledProgramIds);
                    @endphp
                    <div 
                        x-show="activeTab === 'all' 
                            || (activeTab === 'enrolled' && {{ $isEnrolled ? 'true' : 'false' }}) 
                            || (activeTab === 'available' && {{ $isEnrolled ? 'false' : 'true' }})"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-400"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-4"
                        class="relative bg-white shadow-xl border border-slate-200/50 rounded-2xl overflow-hidden h-full flex flex-col transition-transform duration-300 hover:-translate-y-2"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">

                        {{-- Badge --}}
                        @if($isEnrolled)
                            <div class="bg-green-200 text-green-700 text-xs font-semibold px-3 py-1 rounded-b-lg absolute top-0 left-0">
                                Terdaftar
                            </div>
                        @endif

                        <div class="p-6 flex flex-col flex-grow mt-6"> {{-- Tambah mt agar badge tidak numpuk --}}
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $program->name }}</h2>
                            <p class="text-gray-600 mb-4 text-sm leading-relaxed flex-grow">
                                {{ Str::limit($program->description, 120) }}
                            </p>
                            <div class="text-xs text-gray-600 space-y-2 border-t pt-4 mt-auto">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-chalkboard-user w-4 text-center text-blue-500"></i>
                                    <span><strong>Pembimbing:</strong> {{ $program->mentor->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-clock w-4 text-center text-blue-500"></i>
                                    <span><strong>Durasi:</strong> {{ $program->duration_days }} Hari</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-users w-4 text-center text-blue-500"></i>
                                    <span><strong>Peserta:</strong> {{ $program->enrolled_users_count }} orang</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="px-6 pb-6 bg-white">
                            @if($isEnrolled)
                                <a href="{{ route('programs.show', $program) }}" 
                                    class="flex items-center justify-center gap-2 w-full text-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
                                    Lanjutkan Program <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            @else
                                <a href="{{ route('programs.detail', $program->slug) }}" 
                                    class="w-full text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                                    Lihat Detail
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Fallback jika tidak ada program sama sekali -->
                    <div class="md:col-span-2 lg:col-span-3 text-center py-16 px-6 bg-white rounded-2xl shadow-xl border border-slate-200/50">
                        <div class="w-20 h-20 mx-auto bg-slate-100 text-slate-400 flex items-center justify-center rounded-full mb-6">
                            <i class="fa-solid fa-folder-open text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Belum Ada Program</h3>
                        <p class="mt-1 text-sm text-gray-500">Saat ini belum ada program komunitas yang tersedia. Silakan cek kembali nanti.</p>
                    </div>
                @endforelse

                <!-- Fallback per-tab -->
                <div 
                    x-show="activeTab === 'enrolled' && {{ $hasEnrolled ? 'false' : 'true' }}" 
                    x-transition.opacity.duration.400ms
                    class="md:col-span-2 lg:col-span-3 text-center py-12 text-gray-500">
                    Anda belum mengikuti program apapun.
                </div>

                <div 
                    x-show="activeTab === 'available' && {{ $hasAvailable ? 'false' : 'true' }}" 
                    x-transition.opacity.duration.400ms
                    class="md:col-span-2 lg:col-span-3 text-center py-12 text-gray-500">
                    Belum ada program baru yang tersedia.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>