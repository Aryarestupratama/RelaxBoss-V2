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
                   {{-- Header Halaman --}}
                    <div class="text-center">
                        {{-- Gambar RelaxMate --}}
                        <div class="mb-6 flex justify-center">
                            <img src="{{ asset('build/assets/stress-relaxmate.png') }}" 
                            alt="RelaxMate sedang stres" 
                            class="w-40 h-40 sm:w-48 sm:h-48 lg:w-60 lg:h-60 object-contain drop-shadow-md">
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

                        {{-- [DIHAPUS] Bagian Opsi Hotline Psikolog telah dipindahkan --}}

                        {{-- Tombol Aksi --}}
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