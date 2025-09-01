<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Konfirmasi Jadwal Konsultasi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                            <strong class="font-bold">Berhasil!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">Jadwal Anda Telah Dibuat</h3>
                    <p class="text-center text-gray-500 mb-8">Langkah selanjutnya adalah melakukan pembayaran untuk mengkonfirmasi sesi Anda.</p>

                    <div class="border-2 border-gray-200 rounded-lg p-6 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status Booking</span>
                            <span class="font-semibold text-yellow-600 bg-yellow-100 px-2 py-1 rounded-full text-sm capitalize">{{ $session->status }}</span>
                        </div>
                         <div class="flex justify-between">
                            <span class="text-gray-500">Status Pembayaran</span>
                            <span class="font-semibold text-red-600 bg-red-100 px-2 py-1 rounded-full text-sm capitalize">{{ $session->payment_status }}</span>
                        </div>
                        <hr>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Psikolog</span>
                            <span class="font-semibold text-gray-800">{{ $session->psychologist->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jadwal Sesi</span>
                            <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($session->session_start_time)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</span>
                        </div>
                         <div class="flex justify-between items-center">
                            <span class="text-gray-500">Total Biaya</span>
                            <span class="font-bold text-xl text-blue-600">
                                {{ 'Rp ' . number_format($session->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- PERBAIKAN: Tambahkan Form dan simulasi loading dengan AlpineJS -->
                    <div x-data="{ isProcessing: false }" class="mt-8 text-center">
                        <h4 class="font-semibold text-gray-800 mb-3">Metode Pembayaran</h4>
                        <p class="text-gray-600 mb-4">Simulasi Pembayaran untuk Demo</p>
                        
                        <form action="{{ route('booking.pay', $session->id) }}" method="POST" @submit="isProcessing = true">
                            @csrf
                            <button type="submit" 
                                    :disabled="isProcessing"
                                    class="w-full sm:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition flex items-center justify-center disabled:bg-blue-300">
                                <span x-show="!isProcessing">Bayar Sekarang</span>
                                <span x-show="isProcessing">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
