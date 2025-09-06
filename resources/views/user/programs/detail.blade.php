<x-app-layout>
    <x-slot name="title">{{ $program->name }}</x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="bg-white shadow-xl rounded-2xl p-8 mb-8 border border-slate-200/50">
                <div class="flex gap-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $program->name }}</h1>
                        <p class="text-gray-600 mb-4">{{ $program->description }}</p>
                        <p class="text-sm text-gray-500"><i class="fa-solid fa-clock"></i> Durasi {{ $program->duration_days }} Hari</p>
                        <p class="text-sm text-gray-500"><i class="fa-solid fa-user-tie"></i> Mentor: {{ $program->mentor->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Preview Materi --}}
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-slate-200/50 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Preview Materi Lengkap</h2>

                @forelse($program->materials as $material)
                    <div class="border-b py-4 last:border-b-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-blue-600">
                                Hari ke-{{ $material->day_number }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $material->created_at ? $material->created_at->isoFormat('D MMMM YYYY') : '' }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-900">{{ $material->title }}</h3>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-3">
                            {{ Str::limit(strip_tags($material->content), 150) }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 italic">Belum ada materi ditambahkan untuk program ini.</p>
                @endforelse
            </div>


            {{-- Tombol Ikuti Program --}}
            <form action="{{ route('programs.enroll', $program) }}" method="POST" class="text-center">
                @csrf
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                    Ikuti Program Ini
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('programs.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali ke daftar program</a>
            </div>
        </div>
    </div>
</x-app-layout>
