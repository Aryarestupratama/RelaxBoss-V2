@extends('layouts.psychologist')

@section('content')
    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editMaterial: {},
        deleteMaterial: {},
        init() {
            // Logika untuk membuka kembali modal jika ada error validasi
            @if($errors->any())
                @if(session('edit_material_id'))
                    this.editMaterial = {
                        id: {{ session('edit_material_id') }},
                        day_number: '{{ old('day_number') }}',
                        title: '{{ old('title') }}',
                        content: '{{ old('content') }}'
                    };
                    this.showEditModal = true;
                @else
                    this.showCreateModal = true;
                @endif
            @endif
        },
        openEditModal(material) {
            this.editMaterial = JSON.parse(JSON.stringify(material));
            this.showEditModal = true;
        },
        openDeleteModal(material) {
            this.deleteMaterial = material;
            this.showDeleteModal = true;
        }
    }">
        <!-- Header Halaman -->
        <div class="mb-6">
            <a href="{{ route('psikolog.programs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali ke Daftar Program Saya</a>
            <h1 class="text-2xl font-semibold text-gray-800 mt-2">Kelola Materi: {{ $program->name }}</h1>
        </div>

        <!-- Notifikasi -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Konten Utama -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Daftar Materi Harian</h2>
                <button @click="showCreateModal = true" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">Tambah Materi</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hari Ke-</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul Materi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($program->materials->sortBy('day_number') as $material)
                            <tr>
                                <td class="px-6 py-4 font-mono w-px text-center">{{ $material->day_number }}</td>
                                <td class="px-6 py-4 font-medium">{{ $material->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openEditModal({{ $material }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                    <button @click="openDeleteModal({{ $material }})" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada materi untuk program ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modals -->
        @include('psikolog.programs.partials.create-material-modal')
        @include('psikolog.programs.partials.edit-material-modal')
        @include('psikolog.programs.partials.delete-material-modal')
    </div>
@endsection
