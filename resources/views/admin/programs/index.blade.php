@extends('layouts.admin')

@section('content')
    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editProgram: {},
        deleteProgram: {},
        init() {
            // Logika untuk membuka kembali modal jika ada error validasi
            @if($errors->any())
                @if(session('edit_program_id'))
                    this.editProgram = {
                        id: {{ session('edit_program_id') }},
                        name: '{{ old('name') }}',
                        description: '{{ old('description') }}',
                        duration_days: '{{ old('duration_days') }}',
                        mentor_id: '{{ old('mentor_id') }}'
                    };
                    this.showEditModal = true;
                @else
                    this.showCreateModal = true;
                @endif
            @endif
        },
        openEditModal(program) {
            this.editProgram = JSON.parse(JSON.stringify(program));
            this.showEditModal = true;
        },
        openDeleteModal(program) {
            this.deleteProgram = program;
            this.showDeleteModal = true;
        }
    }">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Program Komunitas</h1>
            <button @click="showCreateModal = true" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">
                Tambah Program Baru
            </button>
        </div>

        <!-- Notifikasi -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Tabel Program -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembimbing</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($programs as $program)
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $program->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $program->mentor->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $program->duration_days }} Hari</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                               <a href="{{ route('admin.programs.materials.index', $program) }}" class="text-green-600 hover:text-green-900 mr-4">Kelola Materi</a>
                                <button @click="openEditModal({{ $program }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button @click="openDeleteModal({{ $program }})" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada program yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $programs->links() }}
        </div>

        <!-- Modals -->
        @include('admin.programs.partials.create-modal')
        @include('admin.programs.partials.edit-modal')
        @include('admin.programs.partials.delete-modal')
    </div>
@endsection
