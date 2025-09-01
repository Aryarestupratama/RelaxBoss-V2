<x-app-layout>
    {{-- Mengatur judul tab browser --}}
    <x-slot name="title">
        Langkah Tambahan: Ceritakan Konteks Anda
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Langkah Tambahan: Ceritakan Lebih Lanjut
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-200/50" data-aos="fade-up">
                
                <div class="p-6 sm:p-8">
                    {{-- [IMPROVISASI] Header Halaman yang lebih mendukung --}}
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto bg-yellow-100 text-yellow-600 flex items-center justify-center rounded-full mb-6">
                            <i class="fa-solid fa-pen-to-square text-4xl"></i>
                        </div>
                        <h1 class="mt-4 text-3xl font-extrabold text-gray-900 tracking-tight">
                            Satu Langkah Terakhir
                        </h1>
                        <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                            Hasil Anda menunjukkan tingkat stres yang perlu perhatian lebih. Menceritakan apa yang Anda rasakan dapat membantu kami memberikan rekomendasi yang jauh lebih personal dan relevan.
                        </p>
                    </div>

                    {{-- Notifikasi Error --}}
                    @if ($errors->any())
                        <div class="mt-8 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form Input Konteks --}}
                    <form action="{{ route('quizzes.context.submit', $attempt) }}" method="POST" class="mt-8">
                        @csrf
                        {{-- [IMPROVISASI] Menambahkan character counter dengan AlpineJS --}}
                        <div x-data="{ context: '{{ old('context', '') }}', minLength: 20, get charCount() { return this.context.length } }">
                            <label for="context" class="block text-sm font-medium text-gray-700">
                                Apa yang sedang terjadi atau ada di pikiran Anda saat ini?
                            </label>
                            <div class="mt-1">
                                <textarea id="context" name="context" rows="6" 
                                          x-model="context"
                                          class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                          placeholder="Contoh: Saya merasa tertekan karena tenggat waktu pekerjaan yang sangat ketat minggu ini..."
                                          required 
                                          :minlength="minLength"></textarea>
                            </div>
                            <p class="mt-2 text-xs text-right" :class="charCount >= minLength ? 'text-green-600' : 'text-gray-500'">
                                <span x-text="charCount"></span> / <span x-text="minLength"></span> karakter minimum
                            </p>
                        </div>

                        {{-- [IMPROVISASI] Opsi Hotline yang lebih menonjol --}}
                        <div class="mt-8 p-5 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fa-solid fa-phone-volume h-6 w-6 text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-semibold text-green-800">Butuh Bantuan Segera?</h3>
                                    <p class="text-sm text-green-700 mt-1">
                                        Jika Anda perlu berbicara dengan seseorang secepatnya, tim kami siap membantu mencarikan psikolog yang paling sesuai untuk Anda.
                                    </p>
                                    <div class="mt-3">
                                        <a href="https://wa.me/6281234567890?text=Halo%20RelaxBoss,%20hasil%20asesmen%20saya%20cukup%20tinggi.%20Bisakah%20Anda%20membantu%20saya%20menemukan%20psikolog%20yang%20sesuai?" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition shadow-md">
                                            <i class="fab fa-whatsapp"></i>
                                            Hubungi Hotline via WhatsApp
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- [IMPROVISASI] Tombol Aksi yang lebih jelas --}}
                        <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row justify-end items-center gap-4">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center px-8 py-3 font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-md">
                                Kirim & Dapatkan Rekomendasi
                            </button>
                            <a href="{{ route('quizzes.result', $attempt) }}" class="w-full sm:w-auto text-center px-6 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                                Lewati & Lihat Hasil
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
