<div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="showEditModal" x-transition class="fixed inset-0" @click="showEditModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="showEditModal" x-transition class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-2xl sm:w-full">
            <form :action="`/psikolog/materials/${editMaterial.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Materi</h3>
                    <div class="mt-4 space-y-4">
                        <input type="hidden" name="edit_material_id" :value="editMaterial.id">
                        <div>
                            <label for="edit_m_day_number_psikolog" class="block text-sm font-medium text-gray-700">Hari Ke-</label>
                            <input type="number" name="day_number" id="edit_m_day_number_psikolog" x-model="editMaterial.day_number" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="edit_m_title_psikolog" class="block text-sm font-medium text-gray-700">Judul Materi</label>
                            <input type="text" name="title" id="edit_m_title_psikolog" x-model="editMaterial.title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="edit_m_content_input_psikolog" class="block text-sm font-medium text-gray-700">Konten Materi</label>
                            {{-- [IMPLEMENTASI TRIX] --}}
                            <input id="edit_m_content_input_psikolog" type="hidden" name="content" :value="editMaterial.content">
                            <trix-editor input="edit_m_content_input_psikolog" class="mt-1 trix-content"></trix-editor>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Update</button>
                    <button type="button" @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>