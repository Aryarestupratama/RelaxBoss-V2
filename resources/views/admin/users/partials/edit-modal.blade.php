<!-- Modal Edit Pengguna -->
<div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="showEditModal" x-transition class="fixed inset-0 transition-opacity" @click="showEditModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="showEditModal" x-transition class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form :action="`/admin/users/${editUser.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Pengguna</h3>
                    <div class="mt-4 space-y-4">
                        {{-- Form fields --}}
                        <input type="hidden" name="edit_user_id" :value="editUser.id">
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Nama Panggilan</label>
                            <input type="text" name="name" id="edit_name" x-model="editUser.name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="edit_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="full_name" id="edit_full_name" x-model="editUser.full_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="edit_email" x-model="editUser.email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="edit_password" class="block text-sm font-medium text-gray-700">Password Baru (Opsional)</label>
                            <input type="password" name="password" id="edit_password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="edit_password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="edit_role" class="block text-sm font-medium text-gray-700">Peran</label>
                            <select name="role" id="edit_role" x-model="editUser.role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                                <option value="psikolog">Psikolog</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Update</button>
                    <button type="button" @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
