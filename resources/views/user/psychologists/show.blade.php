<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profil Psikolog
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Kolom Kiri: Info Utama & Aksi -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Kartu Info Utama -->
                    <div class="bg-white shadow-lg rounded-2xl p-6 text-center">
                        <img class="h-32 w-32 rounded-full object-cover ring-4 ring-blue-200 mx-auto" 
                             src="{{ $psychologist->profile_picture ? asset('storage/' . $psychologist->profile_picture) : 'https://placehold.co/128x128/007BFF/FFFFFF?text=' . $psychologist->name[0] }}" 
                             alt="{{ $psychologist->name }}">
                        <h1 class="mt-4 text-2xl font-bold text-gray-900">{{ $psychologist->name }}</h1>
                        <p class="text-blue-600 font-semibold">{{ $psychologist->psychologistProfile->title ?? 'Psikolog' }}</p>
                        <p class="mt-2 text-sm text-gray-500">{{ $psychologist->psychologistProfile->practice_location ?? 'Lokasi tidak tersedia' }}</p>
                    </div>

                    <!-- Kartu Aksi (Jadwalkan Sesi) -->
                    <div class="bg-white shadow-lg rounded-2xl p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Jadwalkan Sesi</h2>
                        <p class="text-sm text-gray-600 mb-4">Pilih jenis layanan untuk melihat jadwal dan memesan sesi konsultasi.</p>
                        {{-- [PERBAIKAN] Mengubah tombol menjadi link ke halaman booking --}}
                        <a href="{{ route('booking.create', $psychologist) }}" class="w-full text-center block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                            Lihat Jadwal & Pesan
                        </a>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail Profil -->
                <div class="lg:col-span-2 bg-white shadow-lg rounded-2xl p-6 sm:p-8">
                    <div class="space-y-8">
                        <!-- Tentang Saya (Bio) -->
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-3">Tentang Saya</h2>
                            <div class="prose prose-sm max-w-none text-gray-600">
                                <p>{{ $psychologist->psychologistProfile->bio ?? 'Bio belum tersedia.' }}</p>
                            </div>
                        </div>

                        <hr>

                        <!-- Bidang Keahlian -->
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-3">Bidang Keahlian</h2>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($psychologist->specializations as $spec)
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-slate-100 text-slate-700">{{ $spec->name }}</span>
                                @empty
                                    <p class="text-sm text-gray-500">Bidang keahlian belum diatur.</p>
                                @endforelse
                            </div>
                        </div>

                        <hr>

                        <!-- Informasi Profesional -->
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-3">Informasi Profesional</h2>
                            <ul class="text-sm space-y-2 text-gray-600">
                                <li><strong>Pendidikan:</strong> {{ $psychologist->psychologistProfile->education ?? 'N/A' }}</li>
                                <li><strong>Pengalaman:</strong> {{ $psychologist->psychologistProfile->years_of_experience ?? 'N/A' }} tahun</li>
                                <li><strong>Nomor STR:</strong> {{ $psychologist->psychologistProfile->str_number ?? 'N/A' }}</li>
                                <li><strong>Nomor SIPP:</strong> {{ $psychologist->psychologistProfile->sipp_number ?? 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
