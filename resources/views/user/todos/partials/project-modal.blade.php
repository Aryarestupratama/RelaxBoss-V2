{{-- MODAL PROYEK YANG SUDAH DIPERBAIKI --}}
<div id="project-modal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center p-4 hidden z-50 transition-opacity duration-300 ease-in-out">
    
    <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl transform transition-all duration-300 ease-in-out">
        
        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 id="project-modal-title" class="text-xl font-bold text-gray-900">
                </h3>
            <button type="button" id="close-project-modal-x" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                <i class="fas fa-times fa-lg"></i>
            </button>
        </div>

        <form id="project-form" class="p-6 space-y-6">
            <input type="hidden" id="project-id" name="id">
            <div>
                <label for="project-name-input" class="block mb-2 text-sm font-medium text-gray-700">Nama Proyek</label>
                <input type="text" id="project-name-input" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Proyek Desain Web" required>
            </div>
            
            <div class="flex items-center justify-end pt-4 border-t border-gray-200 rounded-b gap-3">
                <button type="button" id="close-project-modal" class="px-5 py-2.5 text-gray-600 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium">
                    Batal
                </button>
                <button type="submit" id="save-project-button" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 text-white font-medium rounded-lg text-sm">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>

    </div>
</div>