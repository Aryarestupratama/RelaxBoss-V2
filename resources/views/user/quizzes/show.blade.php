<x-app-layout>
    <x-slot name="title">
        Asesmen: {{ $quiz->name }}
    </x-slot>

    {{-- [IMPROVISASI] Latar belakang gradien untuk suasana yang lebih tenang --}}
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-slate-50 to-emerald-50 py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div x-data="{
                currentStep: 0,
                totalSteps: {{ $quiz->questions->count() }},
                isSubmitting: false,
                selectedOption: null,

                selectAnswerAndAdvance(value) {
                    this.selectedOption = value;
                    if (this.currentStep < this.totalSteps - 1) {
                        setTimeout(() => {
                            this.currentStep++;
                            this.selectedOption = null; // Reset pilihan untuk pertanyaan berikutnya
                        }, 400); // Jeda sedikit lebih lama untuk efek visual
                    } else {
                        this.isSubmitting = true;
                        setTimeout(() => {
                            this.$refs.quizForm.submit();
                        }, 800);
                    }
                },
            }" class="relative bg-white/70 backdrop-blur-xl shadow-2xl rounded-3xl overflow-hidden border border-white/50">

                {{-- Overlay loading --}}
                <div x-show="isSubmitting" x-transition.opacity class="absolute inset-0 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center z-10">
                    <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-4 text-gray-600 font-semibold">Menganalisis jawaban Anda...</p>
                </div>

                <div class="p-6 sm:p-8">
                    {{-- [IMPROVISASI] Header & Progress Bar yang Didesain Ulang --}}
                    <div class="mb-8">
                        <p class="text-sm font-semibold text-blue-600">Pertanyaan <span x-text="currentStep + 1"></span> dari <span x-text="totalSteps"></span></p>
                        <div class="w-full bg-slate-200 rounded-full h-2.5 mt-2">
                            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2.5 rounded-full transition-all duration-500" :style="`width: ${((currentStep + 1) / totalSteps) * 100}%`"></div>
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
                                 x-transition:enter-start="opacity-0 translate-y-8"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="relative min-h-[350px]">
                                <fieldset>
                                    {{-- [IMPROVISASI] Tampilan Pertanyaan yang Lebih Fokus --}}
                                    <legend class="text-2xl font-bold text-center text-gray-800 mb-8">
                                        {{ $question->text }}
                                    </legend>
                                    
                                    {{-- [IMPROVISASI] Opsi Jawaban Menjadi Kartu Interaktif --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach ($quiz->likertOptions->sortBy('value') as $option)
                                            <label class="relative flex items-center p-4 border-2 rounded-xl transition-all duration-200 cursor-pointer"
                                                   :class="{
                                                        'border-blue-500 bg-blue-50 ring-2 ring-blue-200 shadow-lg': selectedOption == {{ $option->value }},
                                                        'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50/50': selectedOption !== {{ $option->value }}
                                                   }">
                                                
                                                <input type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ $option->value }}"
                                                       @change="selectAnswerAndAdvance({{ $option->value }})"
                                                       required
                                                       class="sr-only">
                                                
                                                <span class="font-semibold text-gray-700 text-center w-full">{{ $option->label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            </div>
                        @endforeach

                        {{-- Tombol Navigasi --}}
                        <div class="mt-10 pt-6 border-t flex justify-between items-center">
                            <button type="button" @click="if (currentStep > 0) currentStep--"
                                    class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition flex items-center gap-2"
                                    :class="{ 'opacity-50 cursor-not-allowed': currentStep === 0 }">
                                <i class="fa-solid fa-arrow-left"></i>
                                Sebelumnya
                            </button>
                             <a href="{{ route('quizzes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batalkan</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

