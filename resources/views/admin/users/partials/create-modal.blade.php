<!-- Modal Tambah Pengguna -->
<div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" @click="showCreateModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Pengguna Baru</h3>
                    <div class="mt-4 space-y-4">
                        {{-- Form fields --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Panggilan</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('full_name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                             @error('email')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                             @error('password')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Peran</label>
                            <select name="role" id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                                <option value="psikolog">Psikolog</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                    <button type="button" @click="showCreateModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>