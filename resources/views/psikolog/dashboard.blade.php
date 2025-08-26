@extends('layouts.psychologist')

@section('content')
    <div>
        <!-- Header Sambutan -->
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">Selamat Datang, {{ $psychologist->name }}!</h1>
            <p class="mt-2 text-gray-600">Ini adalah pusat kendali Anda. Kelola program dan jadwal konsultasi Anda dari sini.</p>
        </div>

        <!-- Kartu Aksi Utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Kartu Program Saya -->
            <a href="#" {{-- Nanti ke route('psychologist.programs.index') --}} class="bg-white shadow-md rounded-lg p-6 flex items-center space-x-6 hover:shadow-lg transition-shadow duration-300">
                <div class="bg-blue-100 text-blue-600 p-4 rounded-lg">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Program Saya</h2>
                    <p class="mt-1 text-gray-500 text-sm">Kelola materi dan lihat peserta untuk program yang Anda bimbing.</p>
                </div>
            </a>

            <!-- Kartu Jadwal Konsultasi -->
            <a href="#" {{-- Nanti ke route('psychologist.consultations.index') --}} class="bg-white shadow-md rounded-lg p-6 flex items-center space-x-6 hover:shadow-lg transition-shadow duration-300">
                <div class="bg-green-100 text-green-600 p-4 rounded-lg">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Jadwal Konsultasi</h2>
                    <p class="mt-1 text-gray-500 text-sm">Lihat jadwal sesi konsultasi Anda yang akan datang dan kelola ketersediaan.</p>
                </div>
            </a>
        </div>

        <!-- Placeholder untuk konten lainnya -->
        <div class="mt-12 bg-white shadow-md rounded-lg p-8 text-center text-gray-400">
            <p>Area untuk statistik atau notifikasi penting lainnya akan ditampilkan di sini.</p>
        </div>
    </div>
@endsection
