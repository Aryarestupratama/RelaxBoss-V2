<x-app-layout>
    {{-- Mengatur judul tab browser --}}
    <x-slot name="title">
        Asesmen: {{ $quiz->name }}
    </x-slot>

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
                isSubmitting: false,

                selectAnswerAndAdvance() {
                    if (this.currentStep < this.totalSteps - 1) {
                        setTimeout(() => {
                            this.currentStep++;
                        }, 300);
                    } else {
                        this.isSubmitting = true;
                        setTimeout(() => {
                            this.$refs.quizForm.submit();
                        }, 500);
                    }
                },
            }" class="relative bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-200/50">

                {{-- Overlay loading saat kuis selesai --}}
                <div x-show="isSubmitting" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center z-10" style="display: none;">
                    <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-4 text-gray-600 font-semibold">Menyelesaikan asesmen...</p>
                </div>

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

                    @if (session('error'))
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('quizzes.submit', $quiz) }}" method="POST" id="quizForm" x-ref="quizForm">
                        @csrf
                        
                        @foreach ($quiz->questions as $index => $question)
                            <div x-show="currentStep === {{ $index }}" 
                                 x-transition:enter="transition ease-out duration-300 transform"
                                 x-transition:enter-start="opacity-0 translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="relative min-h-[250px]">
                                <fieldset>
                                    <legend class="text-lg font-semibold text-gray-800 mb-4">
                                        {{ $question->text }}
                                    </legend>
                                    
                                    <div class="space-y-3">
                                        @foreach ($quiz->likertOptions->sortBy('value') as $option)
                                            <label class="flex items-center p-4 border rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-colors duration-200 cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-400 has-[:checked]:ring-2 has-[:checked]:ring-blue-200">
                                                <input type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ $option->value }}"
                                                       {{-- [PERBAIKAN KRITIS] Mengganti @click dengan @change --}}
                                                       @change="selectAnswerAndAdvance()"
                                                       required
                                                       class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                <span class="ml-4 text-sm font-medium text-gray-700 flex-grow">{{ $option->label }}</span>
                                                <i class="fa-solid fa-check text-blue-600 ml-4 hidden has-[:checked]:block"></i>
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