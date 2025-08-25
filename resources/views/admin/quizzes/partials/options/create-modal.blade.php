<div x-show="showCreateOptionModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="showCreateOptionModal" x-transition class="fixed inset-0" @click="showCreateOptionModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="showCreateOptionModal" x-transition class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.quizzes.options.store', $quiz) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Tambah Opsi Jawaban Baru</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="create_o_label" class="block text-sm font-medium text-gray-700">Label Opsi (Teks Jawaban)</label>
                            <input type="text" name="label" id="create_o_label" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="create_o_value" class="block text-sm font-medium text-gray-700">Nilai (Skor)</label>
                            <input type="number" name="value" id="create_o_value" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                    <button type="button" @click="showCreateOptionModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
