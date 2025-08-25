<div x-show="showEditRuleModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="showEditRuleModal" x-transition class="fixed inset-0" @click="showEditRuleModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="showEditRuleModal" x-transition class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form :action="`/admin/rules/${editRule.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Aturan Skor</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="edit_r_sub_scale" class="block text-sm font-medium text-gray-700">Sub-Skala</label>
                            <input type="text" name="sub_scale" id="edit_r_sub_scale" x-model="editRule.sub_scale" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="edit_r_min_score" class="block text-sm font-medium text-gray-700">Skor Minimum</label>
                                <input type="number" name="min_score" id="edit_r_min_score" x-model="editRule.min_score" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="edit_r_max_score" class="block text-sm font-medium text-gray-700">Skor Maksimum</label>
                                <input type="number" name="max_score" id="edit_r_max_score" x-model="editRule.max_score" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label for="edit_r_interpretation" class="block text-sm font-medium text-gray-700">Interpretasi</label>
                            <input type="text" name="interpretation" id="edit_r_interpretation" x-model="editRule.interpretation" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Update</button>
                    <button type="button" @click="showEditRuleModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>