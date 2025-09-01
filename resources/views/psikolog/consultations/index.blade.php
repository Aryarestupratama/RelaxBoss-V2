@extends('layouts.psychologist')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ tab: 'upcoming' }">
                <!-- Navigasi Tab -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="tab = 'upcoming'" 
                                :class="{ 'border-blue-500 text-blue-600': tab === 'upcoming', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'upcoming' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                            Sesi Akan Datang
                        </button>
                        <button @click="tab = 'past'" 
                                :class="{ 'border-blue-500 text-blue-600': tab === 'past', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'past' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                            Riwayat Sesi
                        </button>
                    </nav>
                </div>

                <!-- Konten Tab -->
                <div>
                    <!-- Tab Sesi Akan Datang -->
                    <div x-show="tab === 'upcoming'" x-cloak>
                        <div class="space-y-4">
                            @forelse ($upcomingSessions as $session)
                                <div class="bg-white shadow-md rounded-lg p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center space-x-4 flex-grow">
                                        <img class="h-16 w-16 rounded-full object-cover" 
                                             src="{{ $session->user->profile_picture ? asset('storage/' . $session->user->profile_picture) : 'https://placehold.co/64x64/718096/FFFFFF?text=' . $session->user->name[0] }}" 
                                             alt="{{ $session->user->name }}">
                                        <div>
                                            <p class="font-bold text-gray-800">Sesi dengan {{ $session->user->name }}</p>
                                            <p class="text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($session->session_start_time)->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB
                                            </p>
                                        </div>
                                    </div>
                                    {{-- [PERBAIKAN] Menambahkan tombol rekam medis & memperbaiki layout --}}
                                    <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
                                        @if($session->medicalRecords->isNotEmpty())
                                            <a href="{{ route('psikolog.consultations.records', $session) }}" class="w-full sm:w-auto text-center px-4 py-2 bg-blue-100 text-blue-700 font-semibold rounded-lg shadow-sm hover:bg-blue-200 transition text-sm">
                                                Lihat Rekam Medis
                                            </a>
                                        @endif
                                        <a href="{{ route('consultation.show', $session) }}" class="w-full sm:w-auto text-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-sm hover:bg-green-700 transition text-sm">
                                            Masuk Sesi
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 px-6 bg-white rounded-lg shadow-sm">
                                    <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak Ada Jadwal Mendatang</h3>
                                    <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada sesi konsultasi yang telah dikonfirmasi.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tab Riwayat Sesi -->
                    <div x-show="tab === 'past'" x-cloak>
                        <div class="space-y-4">
                             @forelse ($pastSessions as $session)
                                <div class="bg-white/60 backdrop-blur-sm border border-gray-200 shadow-sm rounded-lg p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center space-x-4">
                                        <img class="h-16 w-16 rounded-full object-cover" 
                                             src="{{ $session->user->profile_picture ? asset('storage/' . $session->user->profile_picture) : 'https://placehold.co/64x64/E2E8F0/4A5568?text=' . $session->user->name[0] }}" 
                                             alt="{{ $session->user->name }}">
                                        <div>
                                            <p class="font-bold text-gray-800">Sesi dengan {{ $session->user->name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($session->session_start_time)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                                            </p>
                                        </div>
                                    </div>
                                    {{-- [PERBAIKAN] Mengarahkan ke halaman catatan & mengubah teks --}}
                                    <a href="{{ route('psikolog.consultations.note', $session) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                                        {{ $session->note ? 'Lihat/Edit Catatan' : 'Tambah Catatan Sesi' }}
                                    </a>
                                </div>
                            @empty
                                <div class="text-center py-12 px-6 bg-white rounded-lg shadow-sm">
                                    <h3 class="text-lg font-medium text-gray-900">Belum Ada Riwayat Sesi</h3>
                                    <p class="mt-1 text-sm text-gray-500">Sesi yang telah selesai atau dibatalkan akan muncul di sini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

