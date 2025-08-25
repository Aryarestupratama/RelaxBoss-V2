<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Asesmen: {{ $quiz->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Komponen Kuis dengan AlpineJS --}}
            <div x-data="{
                currentStep: 0,
                totalSteps: {{ $quiz->questions->count() }},

                // [PERBAIKAN #1] Logika disederhanakan.
                // Fungsi ini sekarang hanya menangani perpindahan UI.
                selectAnswerAndAdvance() {
                    if (this.currentStep < this.totalSteps - 1) {
                        setTimeout(() => {
                            this.currentStep++;
                        }, 300);
                    } else {
                        // Jika ini pertanyaan terakhir, submit form.
                        setTimeout(() => {
                            this.$refs.quizForm.submit();
                        }, 300);
                    }
                },
            }" class="bg-white shadow-xl rounded-2xl overflow-hidden">

                <div class="p-6 sm:p-8">
                    {{-- Header Kuis & Progress Bar --}}
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $quiz->name }}</h1>
                        <p class="mt-2 text-gray-600">Jawab setiap pertanyaan dengan jujur sesuai yang Anda rasakan.</p>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-500">Pertanyaan <span x-text="currentStep + 1"></span> dari <span x-text="totalSteps"></span></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" :style="`width: ${((currentStep + 1) / totalSteps) * 100}%`"></div>
                        </div>
                    </div>

                    {{-- Notifikasi Error --}}
                    @if (session('error'))
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- Form Kuis --}}
                    <form action="{{ route('quizzes.submit', $quiz) }}" method="POST" id="quizForm" x-ref="quizForm">
                        @csrf
                        
                        {{-- [PERBAIKAN #2] Input tersembunyi tidak lagi diperlukan. --}}

                        {{-- Kontainer Pertanyaan --}}
                        {{-- Semua pertanyaan ada di dalam form, tapi hanya satu yang terlihat. --}}
                        @foreach ($quiz->questions as $index => $question)
                            <div x-show="currentStep === {{ $index }}" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="relative min-h-[250px]">
                                <fieldset>
                                    <legend class="text-lg font-semibold text-gray-800 mb-4">
                                        {{ $question->text }}
                                    </legend>
                                    
                                    <div class="space-y-3">
                                        @foreach ($quiz->likertOptions->sortBy('value') as $option)
                                            <label class="flex items-center p-4 border rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-colors duration-200 cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-400 has-[:checked]:ring-2 has-[:checked]:ring-blue-200">
                                                {{-- [PERBAIKAN #3] Input radio sekarang menjadi sumber data utama. --}}
                                                <input type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ $option->value }}"
                                                       @click="selectAnswerAndAdvance()"
                                                       required
                                                       class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                <span class="ml-4 text-sm font-medium text-gray-700">{{ $option->label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            </div>
                        @endforeach

                        {{-- Tombol Navigasi --}}
                        <div class="mt-8 pt-6 border-t flex justify-start">
                            <button type="button" @click="if (currentStep > 0) currentStep--"
                                    class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition"
                                    :class="{ 'opacity-50 cursor-not-allowed': currentStep === 0 }">
                                &larr; Sebelumnya
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-6">
                <a href="{{ route('quizzes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali ke daftar asesmen</a>
            </div>
        </div>
    </div>
</x-app-layout>
