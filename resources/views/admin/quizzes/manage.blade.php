@extends('layouts.admin')

@section('content')
    <div x-data="{
        activeTab: 'questions',
        // State untuk modal Pertanyaan
        showCreateQuestionModal: false,
        showEditQuestionModal: false,
        showDeleteQuestionModal: false,
        editQuestion: {},
        deleteQuestion: {},
        // State untuk modal Opsi Jawaban
        showCreateOptionModal: false,
        showEditOptionModal: false,
        showDeleteOptionModal: false,
        editOption: {},
        deleteOption: {},
        // [BARU] State untuk modal Aturan Skor
        showCreateRuleModal: false,
        showEditRuleModal: false,
        showDeleteRuleModal: false,
        editRule: {},
        deleteRule: {},
        init() {
            // ... (logika init untuk error validasi bisa ditambahkan di sini jika perlu)
        },
        // Fungsi untuk modal Pertanyaan
        openEditQuestionModal(question) {
            this.editQuestion = JSON.parse(JSON.stringify(question));
            this.showEditQuestionModal = true;
        },
        openDeleteQuestionModal(question) {
            this.deleteQuestion = question;
            this.showDeleteQuestionModal = true;
        },
        // Fungsi untuk modal Opsi Jawaban
        openEditOptionModal(option) {
            this.editOption = JSON.parse(JSON.stringify(option));
            this.showEditOptionModal = true;
        },
        openDeleteOptionModal(option) {
            this.deleteOption = option;
            this.showDeleteOptionModal = true;
        },
        // [BARU] Fungsi untuk modal Aturan Skor
        openEditRuleModal(rule) {
            this.editRule = JSON.parse(JSON.stringify(rule));
            this.showEditRuleModal = true;
        },
        openDeleteRuleModal(rule) {
            this.deleteRule = rule;
            this.showDeleteRuleModal = true;
        }
    }">
        <!-- Header Halaman -->
        <div class="mb-6">
            <a href="{{ route('admin.quizzes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali ke Daftar Kuis</a>
            <h1 class="text-2xl font-semibold text-gray-800 mt-2">{{ $quiz->name }}</h1>
            <p class="mt-1 text-gray-600">{{ $quiz->description }}</p>
        </div>

        <!-- Sistem Tab -->
        <div>
            <!-- Tombol Tab -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-6">
                    <button @click="activeTab = 'questions'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'questions' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Pertanyaan ({{ $quiz->questions->count() }})
                    </button>
                    <button @click="activeTab = 'options'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'options' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Opsi Jawaban ({{ $quiz->likertOptions->count() }})
                    </button>
                    <button @click="activeTab = 'rules'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'rules' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Aturan Skor ({{ $quiz->scoringRules->count() }})
                    </button>
                    <button @click="activeTab = 'attempts'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'attempts' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Hasil Pengerjaan ({{ $quiz->attempts->count() }})
                    </button>
                </nav>
            </div>

            <!-- Konten Tab -->
            <div class="mt-6">
                {{-- Konten untuk Tab Pertanyaan --}}
                <div x-show="activeTab === 'questions'" x-cloak>
                    @include('admin.quizzes.partials.questions-tab')
                </div>

                {{-- Konten untuk Tab Opsi Jawaban --}}
                <div x-show="activeTab === 'options'" x-cloak>
                     @include('admin.quizzes.partials.options-tab')
                </div>

                {{-- [BARU] Konten untuk Tab Aturan Skor --}}
                <div x-show="activeTab === 'rules'" x-cloak>
                     @include('admin.quizzes.partials.rules-tab')
                </div>
                
                <div x-show="activeTab === 'attempts'" x-cloak>
                    @include('admin.quizzes.partials.attempts-tab')
                </div>
            </div>
        </div>

        <!-- Modals untuk Pertanyaan -->
        @include('admin.quizzes.partials.questions.create-modal')
        @include('admin.quizzes.partials.questions.edit-modal')
        @include('admin.quizzes.partials.questions.delete-modal')

        <!-- Modals untuk Opsi Jawaban -->
        @include('admin.quizzes.partials.options.create-modal')
        @include('admin.quizzes.partials.options.edit-modal')
        @include('admin.quizzes.partials.options.delete-modal')

        <!-- [BARU] Modals untuk Aturan Skor -->
        @include('admin.quizzes.partials.rules.create-modal')
        @include('admin.quizzes.partials.rules.edit-modal')
        @include('admin.quizzes.partials.rules.delete-modal')
    </div>
@endsection
