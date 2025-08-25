<div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="showCreateModal" x-transition class="fixed inset-0" @click="showCreateModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="showCreateModal" x-transition class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.quizzes.questions.store', $quiz) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Tambah Pertanyaan Baru</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="create_q_text" class="block text-sm font-medium text-gray-700">Teks Pertanyaan</label>
                            <textarea name="text" id="create_q_text" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('text') }}</textarea>
                            @error('text')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="create_q_sub_scale" class="block text-sm font-medium text-gray-700">Sub-Skala (Contoh: Stres, Kecemasan)</label>
                            <input type="text" name="sub_scale" id="create_q_sub_scale" value="{{ old('sub_scale') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('sub_scale')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_reversed" id="create_q_is_reversed" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="create_q_is_reversed" class="ml-2 block text-sm text-gray-900">Pertanyaan ini memiliki skor terbalik</label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                    <button type="button" @click="showCreateModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
