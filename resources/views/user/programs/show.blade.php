<x-app-layout>
    {{-- Mengatur judul tab browser --}}
    <x-slot name="title">
        Program: {{ $program->name }}
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Program: {{ $program->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                
                {{-- [IMPROVISASI] Kolom Kiri: Sidebar Progres & Navigasi --}}
                <div class="lg:col-span-1 space-y-8 lg:sticky lg:top-8 self-start" data-aos="fade-right">
                    <!-- Kartu Progres Utama -->
                    <div class="bg-white shadow-xl rounded-2xl border border-slate-200/50 p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <img class="h-16 w-16 object-cover rounded-lg" src="{{ $program->cover_image ? asset('storage/' . $program->cover_image) : 'https://placehold.co/100x100/E0F2FE/334155?text=Img' }}" alt="Cover Program">
                            <div>
                                <h1 class="font-bold text-gray-900 leading-tight">{{ $program->name }}</h1>
                                <p class="text-xs text-gray-500">Oleh: {{ $program->mentor->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-500">Progres Anda</span>
                            <span class="text-sm font-medium text-blue-600">{{ floor(($enrollment->current_day / $program->duration_days) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($enrollment->current_day / $program->duration_days) * 100 }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Navigasi Materi Harian -->
                    <div class="bg-white shadow-xl rounded-2xl border border-slate-200/50 p-4">
                        <h2 class="text-md font-bold text-gray-800 mb-2 px-2">Materi Harian</h2>
                        <ul class="space-y-1">
                            @for ($day = 1; $day <= $program->duration_days; $day++)
                                @php
                                    $isCompleted = $day < $enrollment->current_day;
                                    $isActive = $day == $enrollment->current_day;
                                    $isLocked = $day > $enrollment->current_day;
                                    $materialExists = $materials->contains('day_number', $day);
                                @endphp
                                <li>
                                    <a href="{{ $materialExists && !$isLocked ? '#day-' . $day : '#' }}" 
                                       class="flex items-center gap-3 p-3 rounded-lg transition-colors text-sm font-medium
                                              {{ $isActive ? 'bg-blue-100 text-blue-700' : '' }}
                                              {{ $isCompleted && $materialExists ? 'text-gray-700 hover:bg-slate-100' : '' }}
                                              {{ $isLocked ? 'text-gray-400 cursor-not-allowed' : '' }}
                                              {{ !$materialExists ? 'text-gray-400 cursor-default italic' : '' }}">
                                        
                                        @if($isCompleted && $materialExists)
                                            <i class="fa-solid fa-circle-check text-green-500 w-5 text-center"></i>
                                        @elseif($isActive)
                                            <i class="fa-solid fa-circle-play text-blue-500 w-5 text-center animate-pulse"></i>
                                        @else
                                            <i class="fa-solid {{ $isLocked ? 'fa-lock' : 'fa-circle' }} text-gray-300 w-5 text-center"></i>
                                        @endif

                                        <span class="flex-grow">{{ $materialExists ? 'Hari ke-' . $day . ': ' . $materials->firstWhere('day_number', $day)->title : 'Hari ke-' . $day . ': Segera Hadir' }}</span>
                                    </a>
                                </li>
                            @endfor
                        </ul>
                    </div>
                </div>

                {{-- [IMPROVISASI] Kolom Kanan: Feed Materi --}}
                <div class="lg:col-span-2 mt-8 lg:mt-0">
                    <div class="space-y-8">
                        @forelse ($materials as $material)
                            <div id="day-{{ $material->day_number }}" class="bg-white shadow-xl rounded-2xl p-6 sm:p-8 border border-slate-200/50 scroll-mt-8" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Hari ke-{{ $material->day_number }}
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $material->created_at->isoFormat('dddd, D MMMM YYYY') }}</span>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $material->title }}</h2>
                                <div class="prose prose-blue max-w-none text-gray-700 leading-relaxed">
                                    {!! $material->content !!}
                                </div>
                            </div>
                        @empty
                            <div class="bg-white shadow-xl rounded-2xl p-8 text-center border border-slate-200/50">
                                <div class="w-20 h-20 mx-auto bg-slate-100 text-slate-400 flex items-center justify-center rounded-full mb-6">
                                    <i class="fa-solid fa-hourglass-start text-4xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Program Anda Segera Dimulai</h3>
                                <p class="mt-1 text-sm text-gray-500">Materi pertama untuk program ini akan tersedia besok. Persiapkan diri Anda untuk memulai perjalanan ini!</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="text-center mt-12">
                        <a href="{{ route('programs.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali ke daftar program</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
