@extends('layouts.admin')

@section('content')
    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editSpecialization: {},
        deleteSpecialization: {},
        init() {
            // Logika untuk membuka kembali modal jika ada error validasi
            @if($errors->any())
                @if(session('edit_specialization_id'))
                    this.editSpecialization = {
                        id: {{ session('edit_specialization_id') }},
                        name: '{{ old('name') }}',
                        description: '{{ old('description') }}'
                    };
                    this.showEditModal = true;
                @else
                    this.showCreateModal = true;
                @endif
            @endif
        },
        openEditModal(specialization) {
            this.editSpecialization = JSON.parse(JSON.stringify(specialization));
            this.showEditModal = true;
        },
        openDeleteModal(specialization) {
            this.deleteSpecialization = specialization;
            this.showDeleteModal = true;
        }
    }">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Bidang Keahlian</h1>
            <button @click="showCreateModal = true" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">
                Tambah Bidang Baru
            </button>
        </div>

        <!-- Notifikasi -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Tabel Bidang Keahlian -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Bidang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($specializations as $specialization)
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $specialization->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ Str::limit($specialization->description, 80) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal({{ $specialization }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button @click="openDeleteModal({{ $specialization }})" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada bidang keahlian yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $specializations->links() }}
        </div>

        <!-- Modals -->
        @include('admin.specializations.partials.create-modal')
        @include('admin.specializations.partials.edit-modal')
        @include('admin.specializations.partials.delete-modal')
    </div>
@endsection
