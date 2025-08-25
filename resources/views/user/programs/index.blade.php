<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Komunitas & Program Terbimbing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Halaman -->
            <div class="text-center mb-12" data-aos="fade-down">
                <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight sm:text-5xl">
                    Tumbuh Bersama Komunitas
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                    Temukan dukungan dan bimbingan dalam program terstruktur yang dirancang untuk membantu Anda mencapai tujuan kesejahteraan.
                </p>
            </div>

            <!-- Notifikasi Sukses/Error -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Grid untuk Kartu Program -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($programs as $program)
                    <div class="bg-white shadow-lg rounded-2xl overflow-hidden h-full flex flex-col transition-transform duration-300 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <!-- Gambar Cover -->
                        <img class="h-48 w-full object-cover" src="{{ $program->cover_image ? asset('storage/' . $program->cover_image) : 'https://placehold.co/600x400/E0F2FE/334155?text=Program' }}" alt="Cover Program {{ $program->name }}">

                        <div class="p-6 flex flex-col flex-grow">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $program->name }}</h2>
                            <p class="text-gray-600 mb-4 text-sm leading-relaxed flex-grow">{{ Str::limit($program->description, 120) }}</p>
                            
                            <!-- Info Pembimbing & Durasi -->
                            <div class="text-xs text-gray-500 space-y-2 border-t pt-4 mt-4">
                                <p><strong>Pembimbing:</strong> {{ $program->mentor->name ?? 'N/A' }}</p>
                                <p><strong>Durasi:</strong> {{ $program->duration_days }} Hari</p>
                                <p><strong>Peserta:</strong> {{ $program->enrolled_users_count }} orang telah bergabung</p>
                            </div>
                        </div>
                        
                        <div class="px-6 pb-6 bg-white mt-auto">
                            @if(in_array($program->id, $enrolledProgramIds))
                                {{-- Jika user sudah terdaftar --}}
                                <a href="{{ route('programs.show', $program) }}" class="block w-full text-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition">
                                    Lanjutkan Program
                                </a>
                            @else
                                {{-- Jika user belum terdaftar --}}
                                <form action="{{ route('programs.enroll', $program) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                                        Ikuti Program
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 lg:col-span-3 text-center py-16 px-6 bg-white rounded-lg shadow-md">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Belum Ada Program</h3>
                        <p class="mt-1 text-sm text-gray-500">Saat ini belum ada program komunitas yang tersedia. Silakan cek kembali nanti.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
