<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sesi Konsultasi dengan {{ Auth::id() == $session->user_id ? $session->psychologist->name : $session->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                <!-- Header Chat -->
                <div class="p-4 border-b bg-gray-50">
                    <p class="font-semibold text-gray-800">
                        Jadwal: {{ \Carbon\Carbon::parse($session->session_start_time)->isoFormat('dddd, D MMM YYYY, HH:mm') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Sesi akan berakhir pada: {{ \Carbon\Carbon::parse($session->session_end_time)->isoFormat('HH:mm') }} WIB
                    </p>
                </div>

                <!-- Area Pesan -->
                <div class="p-6 h-96 overflow-y-auto space-y-4">
                    @forelse($session->chats as $chat)
                        @if($chat->sender_id === Auth::id())
                            <!-- Pesan Pengguna (Kanan) -->
                            <div class="flex justify-end">
                                <div class="bg-blue-500 text-white p-3 rounded-l-lg rounded-br-lg max-w-xs">
                                    <p class="text-sm">{{ $chat->message }}</p>
                                    <p class="text-xs text-blue-100 mt-1 text-right">{{ $chat->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        @else
                            <!-- Pesan Psikolog (Kiri) -->
                            <div class="flex justify-start">
                                <div class="bg-gray-200 text-gray-800 p-3 rounded-r-lg rounded-bl-lg max-w-xs">
                                    <p class="text-sm">{{ $chat->message }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $chat->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center text-gray-400 pt-16">
                            <p>Belum ada pesan dalam sesi ini.</p>
                            <p class="text-sm">Mulai percakapan pertama Anda!</p>
                        </div>
                    @endforelse
                </div>

                <!-- Input Pesan -->
                <div class="p-4 border-t bg-gray-50">
                    <form action="#" method="POST">
                        @csrf
                        <div class="flex items-center space-x-3">
                            <input type="text" name="message" placeholder="Ketik pesan Anda..." autocomplete="off" class="flex-grow w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <button type="submit" class="bg-blue-600 text-white rounded-full p-3 hover:bg-blue-700 transition shadow">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
