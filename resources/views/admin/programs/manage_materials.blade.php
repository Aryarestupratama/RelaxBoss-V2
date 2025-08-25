@extends('layouts.admin')

@section('content')
    <div x-data="{
        activeTab: 'materials',
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
            <a href="{{ route('admin.programs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali ke Daftar Program</a>
            <h1 class="text-2xl font-semibold text-gray-800 mt-2">Kelola Program: {{ $program->name }}</h1>
        </div>

        <!-- Sistem Tab -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-6">
                <button @click="activeTab = 'materials'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'materials' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                    Materi Program ({{ $program->materials->count() }})
                </button>
                {{-- [BARU] Tombol untuk tab Peserta --}}
                <button @click="activeTab = 'participants'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'participants' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                    Peserta Terdaftar ({{ $program->enrollments->count() }})
                </button>
            </nav>
        </div>

        <!-- Konten Tab -->
        <div class="mt-6">
            <div x-show="activeTab === 'materials'" x-cloak>
                @include('admin.programs.partials.materials-tab')
            </div>
            {{-- [BARU] Konten untuk tab Peserta --}}
            <div x-show="activeTab === 'participants'" x-cloak>
                @include('admin.programs.partials.participants-tab')
            </div>
        </div>

        <!-- Modals -->
        @include('admin.programs.partials.materials.create-modal')
        @include('admin.programs.partials.materials.edit-modal')
        @include('admin.programs.partials.materials.delete-modal')
    </div>
@endsection
