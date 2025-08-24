<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Langkah Tambahan: Ceritakan Lebih Lanjut
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-8" data-aos="fade-up">

                {{-- Header Halaman --}}
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    <h1 class="mt-4 text-3xl font-extrabold text-gray-900 tracking-tight">
                        Satu Langkah Terakhir
                    </h1>
                    <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                        Hasil Anda menunjukkan tingkat stres yang perlu perhatian lebih. Menceritakan apa yang Anda rasakan dapat membantu kami memberikan rekomendasi yang jauh lebih personal dan relevan.
                    </p>
                </div>

                {{-- Notifikasi Error --}}
                @if ($errors->any())
                    <div class="mt-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
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
                    <div>
                        <label for="context" class="block text-sm font-medium text-gray-700">
                            Apa yang sedang terjadi atau ada di pikiran Anda saat ini? (Minimal 20 karakter)
                        </label>
                        <div class="mt-1">
                            <textarea id="context" name="context" rows="6" 
                                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                      placeholder="Contoh: Saya merasa tertekan karena tenggat waktu pekerjaan yang sangat ketat minggu ini..."
                                      required minlength="20">{{ old('context') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <a href="{{ route('quizzes.result', $attempt) }}" class="px-6 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                            Lewati & Lihat Hasil
                        </a>
                        <button type="submit" class="px-6 py-2.5 font-semibold text-white bg-blue-600 rounded-full hover:bg-blue-700 transition">
                            Kirim & Dapatkan Rekomendasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
