@extends('layouts.admin')

@section('content')
    <div x-data="{
        activeTab: '{{ $activeTab }}',
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editUser: {},
        deleteUser: {},
        init() {
            // Jika ada error validasi, buka kembali modal yang relevan
            @if($errors->has('name') || $errors->has('email') || $errors->has('password'))
                @if(session('edit_user_id'))
                    // Jika error terjadi saat mengedit
                    this.editUser = { id: {{ session('edit_user_id') }} }; // Ambil data lengkap jika perlu
                    this.showEditModal = true;
                @else
                    // Jika error terjadi saat membuat
                    this.showCreateModal = true;
                @endif
            @endif
        },
        openEditModal(user) {
            this.editUser = JSON.parse(JSON.stringify(user)); // Salin objek agar tidak reaktif
            this.showEditModal = true;
        },
        openDeleteModal(user) {
            this.deleteUser = user;
            this.showDeleteModal = true;
        }
    }">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Manajemen Pengguna</h1>

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

        <!-- Sistem Tab dan Tombol Tambah -->
        <div class="flex justify-between items-center mb-4">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-6">
                    <a href="{{ route('admin.users.index', ['tab' => 'all']) }}" :class="{ 'border-blue-500 text-blue-600': activeTab === 'all' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Semua</a>
                    <a href="{{ route('admin.users.index', ['tab' => 'admin']) }}" :class="{ 'border-blue-500 text-blue-600': activeTab === 'admin' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Admin</a>
                    <a href="{{ route('admin.users.index', ['tab' => 'psikolog']) }}" :class="{ 'border-blue-500 text-blue-600': activeTab === 'psikolog' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Psikolog</a>
                    <a href="{{ route('admin.users.index', ['tab' => 'user']) }}" :class="{ 'border-blue-500 text-blue-600': activeTab === 'user' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">User</a>
                </nav>
            </div>
            <button @click="showCreateModal = true" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">Tambah Pengguna</button>
        </div>

        <!-- Tabel Pengguna -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peran</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->role->value === 'admin' ? 'bg-red-100 text-red-800' : ($user->role->value === 'psikolog' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($user->role->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal({{ $user }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button @click="openDeleteModal({{ $user }})" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->links() }}
        </div>

        <!-- Modal Tambah Pengguna -->
        @include('admin.users.partials.create-modal')

        <!-- Modal Edit Pengguna -->
        @include('admin.users.partials.edit-modal')

        <!-- Modal Hapus Pengguna -->
        @include('admin.users.partials.delete-modal')

    </div>
@endsection
