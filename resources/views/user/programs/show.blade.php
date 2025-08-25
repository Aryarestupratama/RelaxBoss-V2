<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Program: {{ $program->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Program -->
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden mb-8" data-aos="fade-down">
                <div class="p-6 sm:p-8">
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $program->name }}</h1>
                    <p class="mt-2 text-gray-600">Dibimbing oleh: <strong>{{ $program->mentor->name ?? 'N/A' }}</strong></p>
                    <div class="mt-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-500">Progres Anda</span>
                            <span class="text-sm font-medium text-blue-600">{{ floor(($enrollment->current_day / $program->duration_days) * 100) }}% Selesai</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($enrollment->current_day / $program->duration_days) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feed Materi Harian -->
            <div class="space-y-8">
                @forelse ($materials as $material)
                    <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-8" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                Hari ke-{{ $material->day_number }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $material->created_at->format('d F Y') }}</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-3">{{ $material->title }}</h2>
                        <div class="prose prose-blue max-w-none text-gray-700 leading-relaxed">
                            {!! $material->content !!} {{-- Menggunakan {!! !!} untuk merender HTML dari konten --}}
                        </div>
                    </div>
                @empty
                    <div class="bg-white shadow-md rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Materi Belum Tersedia</h3>
                        <p class="mt-1 text-sm text-gray-500">Materi pertama untuk program ini akan tersedia besok. Silakan cek kembali nanti!</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('programs.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali ke daftar program</a>
            </div>

        </div>
    </div>
</x-app-layout>
