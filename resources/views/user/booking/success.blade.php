<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pembayaran Berhasil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg text-center p-8">
                
                <!-- Ikon Centang -->
                <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

                <h3 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Berhasil!</h3>
                <p class="text-gray-600 mb-6">Sesi konsultasi Anda dengan <strong>{{ $session->psychologist->name }}</strong> telah dikonfirmasi.</p>

                <div class="border-t border-b border-gray-200 py-4 my-6 text-left">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Status Sesi</span>
                        <span class="font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full text-sm capitalize">{{ $session->status }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Jadwal</span>
                        <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($session->session_start_time)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</span>
                    </div>
                </div>

                <p class="text-sm text-gray-500 mb-6">Anda akan menerima notifikasi pengingat sebelum sesi dimulai. Detail sesi juga dapat dilihat di halaman dashboard Anda.</p>

                <a href="{{ route('psychologists.index') }}" class="inline-block px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                    Kembali ke Dashboard
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
