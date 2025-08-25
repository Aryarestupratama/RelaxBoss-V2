<!-- Modal Edit Kuis -->
<div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="showEditModal" x-transition class="fixed inset-0 transition-opacity" @click="showEditModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="showEditModal" x-transition class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form :action="`/admin/quizzes/${editQuiz.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Kuis</h3>
                    <div class="mt-4 space-y-4">
                        <input type="hidden" name="edit_quiz_id" :value="editQuiz.id">
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Nama Kuis</label>
                            <input type="text" name="name" id="edit_name" x-model="editQuiz.name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="edit_description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="edit_description" x-model="editQuiz.description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <div>
                            <label for="edit_score_multiplier" class="block text-sm font-medium text-gray-700">Pengali Skor</label>
                            <input type="number" step="0.01" name="score_multiplier" id="edit_score_multiplier" x-model="editQuiz.score_multiplier" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
