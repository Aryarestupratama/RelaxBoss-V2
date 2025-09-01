<x-app-layout>
    {{-- Mengatur judul tab browser --}}
    <x-slot name="title">
        Komunitas & Program
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- [IMPROVISASI] Header Halaman yang lebih visual -->
            <div class="text-center mb-12" data-aos="fade-down">
                <div class="w-20 h-20 mx-auto bg-blue-100 text-blue-600 flex items-center justify-center rounded-full mb-6">
                    <i class="fa-solid fa-users-line text-4xl"></i>
                </div>
                <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight sm:text-5xl">
                    Tumbuh Bersama Komunitas
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                    Temukan dukungan dan bimbingan dalam program terstruktur yang dirancang untuk membantu Anda mencapai tujuan kesejahteraan.
                </p>
            </div>
            
            <!-- [IMPROVISASI] Filter Interaktif dengan AlpineJS -->
            <div x-data="{ activeTab: 'all' }" class="mb-10">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex justify-center space-x-6" aria-label="Tabs">
                        <button @click="activeTab = 'all'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'all', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'all' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Semua Program
                        </button>
                        <button @click="activeTab = 'enrolled'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'enrolled', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'enrolled' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Program Saya
                        </button>
                         <button @click="activeTab = 'available'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'available', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'available' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Program Tersedia
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Grid untuk Kartu Program -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($programs as $program)
                    @php
                        $isEnrolled = in_array($program->id, $enrolledProgramIds);
                    @endphp
                    <div x-show="(activeTab === 'all') || (activeTab === 'enrolled' && {{ $isEnrolled ? 'true' : 'false' }}) || (activeTab === 'available' && !{{ $isEnrolled ? 'true' : 'false' }})" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 transform scale-95" 
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="relative bg-white shadow-xl border border-slate-200/50 rounded-2xl overflow-hidden h-full flex flex-col transition-transform duration-300 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        
                        {{-- [IMPROVISASI] Badge jika sudah terdaftar --}}
                        @if($isEnrolled)
                            <span class="absolute top-4 right-4 text-xs font-semibold inline-block py-1 px-3 uppercase rounded-full text-green-600 bg-green-200 z-10">
                                Terdaftar
                            </span>
                        @endif

                       
                        <div class="p-6 flex flex-col flex-grow">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $program->name }}</h2>
                            <p class="text-gray-600 mb-4 text-sm leading-relaxed flex-grow">{{ Str::limit($program->description, 120) }}</p>
                            
                            {{-- [IMPROVISASI] Info detail dengan ikon --}}
                            <div class="text-xs text-gray-600 space-y-2 border-t pt-4 mt-auto">
                                <div class="flex items-center gap-2"><i class="fa-solid fa-chalkboard-user w-4 text-center text-blue-500"></i><span><strong>Pembimbing:</strong> {{ $program->mentor->name ?? 'N/A' }}</span></div>
                                <div class="flex items-center gap-2"><i class="fa-solid fa-clock w-4 text-center text-blue-500"></i><span><strong>Durasi:</strong> {{ $program->duration_days }} Hari</span></div>
                                <div class="flex items-center gap-2"><i class="fa-solid fa-users w-4 text-center text-blue-500"></i><span><strong>Peserta:</strong> {{ $program->enrolled_users_count }} orang telah bergabung</span></div>
                            </div>
                        </div>
                        
                        <div class="px-6 pb-6 bg-white">
                            @if($isEnrolled)
                                <a href="{{ route('programs.show', $program) }}" class="flex items-center justify-center gap-2 w-full text-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
                                    Lanjutkan Program <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            @else
                                <form action="{{ route('programs.enroll', $program) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                                        Ikuti Program
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- [IMPROVISASI] Tampilan kosong yang lebih menarik --}}
                    <div class="md:col-span-2 lg:col-span-3 text-center py-16 px-6 bg-white rounded-2xl shadow-xl border border-slate-200/50">
                        <div class="w-20 h-20 mx-auto bg-slate-100 text-slate-400 flex items-center justify-center rounded-full mb-6">
                            <i class="fa-solid fa-folder-open text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Belum Ada Program</h3>
                        <p class="mt-1 text-sm text-gray-500">Saat ini belum ada program komunitas yang tersedia. Silakan cek kembali nanti.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
