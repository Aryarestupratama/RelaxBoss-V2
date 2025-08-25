<div x-show="showEditQuestionModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="showEditQuestionModal" x-transition class="fixed inset-0" @click="showEditQuestionModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="showEditQuestionModal" x-transition class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form :action="`/admin/questions/${editQuestion.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Pertanyaan</h3>
                    <div class="mt-4 space-y-4">
                        <input type="hidden" name="edit_question_id" :value="editQuestion.id">
                        <div>
                            <label for="edit_q_text" class="block text-sm font-medium text-gray-700">Teks Pertanyaan</label>
                            <textarea name="text" id="edit_q_text" x-model="editQuestion.text" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <div>
                            <label for="edit_q_sub_scale" class="block text-sm font-medium text-gray-700">Sub-Skala</label>
                            <input type="text" name="sub_scale" id="edit_q_sub_scale" x-model="editQuestion.sub_scale" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_reversed" id="edit_q_is_reversed" value="1" x-model="editQuestion.is_reversed" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="edit_q_is_reversed" class="ml-2 block text-sm text-gray-900">Pertanyaan ini memiliki skor terbalik</label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Update</button>
                    <button type="button" @click="showEditQuestionModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>