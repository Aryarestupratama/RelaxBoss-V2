@extends('layouts.admin')

@section('content')
    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editQuiz: {},
        deleteQuiz: {},
        init() {
            // Jika ada error validasi, buka kembali modal yang relevan
            @if($errors->any())
                @if(session('edit_quiz_id'))
                    this.editQuiz = {
                        id: {{ session('edit_quiz_id') }},
                        name: '{{ old('name') }}',
                        description: '{{ old('description') }}',
                        score_multiplier: '{{ old('score_multiplier') }}'
                    };
                    this.showEditModal = true;
                @else
                    this.showCreateModal = true;
                @endif
            @endif
        },
        openEditModal(quiz) {
            this.editQuiz = JSON.parse(JSON.stringify(quiz));
            this.showEditModal = true;
        },
        openDeleteModal(quiz) {
            this.deleteQuiz = quiz;
            this.showDeleteModal = true;
        }
    }">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Kuis Asesmen</h1>
            <button @click="showCreateModal = true" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Tambah Kuis Baru
            </button>
        </div>

        <!-- Notifikasi Sukses/Error -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Tabel Kuis -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kuis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($quizzes as $quiz)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $quiz->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ Str::limit($quiz->description, 80) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="text-green-600 hover:text-green-900 mr-4">Kelola</a>
                                <button @click="openEditModal({{ $quiz }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button @click="openDeleteModal({{ $quiz }})" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada kuis yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $quizzes->links() }}
        </div>

        <!-- Modal Tambah Kuis -->
        @include('admin.quizzes.partials.create-modal')

        <!-- Modal Edit Kuis -->
        @include('admin.quizzes.partials.edit-modal')

        <!-- Modal Hapus Kuis -->
        @include('admin.quizzes.partials.delete-modal')
    </div>
@endsection
