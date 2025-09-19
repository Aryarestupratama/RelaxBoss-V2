{{-- MODAL TUGAS (DIREVISI AGAR SESUAI DENGAN JS YANG ADA) --}}
<div id="task-modal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center p-4 hidden z-50 transition-opacity duration-300 ease-in-out">
    
    <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl transform transition-all duration-300 ease-in-out flex flex-col max-h-[90vh]">
        
        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 id="task-modal-title" class="text-xl font-bold text-gray-900">
                </h3>
            <button type="button" id="close-task-modal-x" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                <i class="fas fa-times fa-lg"></i>
            </button>
        </div>

        <div class="p-6 flex-grow overflow-y-auto">
            <form id="task-form" class="space-y-4">
                <input type="hidden" id="task-id" name="id">
                
                <div>
                    <label for="task-title" class="block mb-2 text-sm font-medium text-gray-700">Judul Tugas</label>
                    <input type="text" id="task-title" name="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <div>
                    <label for="task-notes" class="block mb-2 text-sm font-medium text-gray-700">Catatan / Deskripsi</label>
                    <textarea id="task-notes" name="notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="task-project" class="block mb-2 text-sm font-medium text-gray-700">Proyek</label>
                        <select id="task-project" name="project_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">Tanpa Proyek</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="task-priority" class="block mb-2 text-sm font-medium text-gray-700">Prioritas</label>
                        <select id="task-priority" name="priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="low">Rendah</option>
                            <option value="medium">Sedang</option>
                            <option value="high">Tinggi</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="task-eisenhower" class="block mb-2 text-sm font-medium text-gray-700">Kuadran Eisenhower</label>
                        <select id="task-eisenhower" name="eisenhower_quadrant" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">Tidak ditentukan</option>
                            <option value="do">Lakukan Segera</option>
                            <option value="schedule">Jadwalkan</option>
                            <option value="delegate">Delegasikan</option>
                            <option value="delete">Eliminasi</option>
                        </select>
                    </div>
                    <div>
                        <label for="task-duration" class="block mb-2 text-sm font-medium text-gray-700">Durasi Pomodoro Kustom (menit)</label>
                        <input type="number" id="task-duration" name="pomodoro_custom_duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" min="1">
                    </div>
                </div>

                <div>
                    <label for="task-due-date" class="block mb-2 text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                    <input type="datetime-local" id="task-due-date" name="due_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-medium text-gray-700">Sub-tugas</label>
                        <button type="button" id="add-subtask-button" class="text-sm text-blue-600 hover:text-blue-800 font-semibold"><i class="fas fa-plus mr-1"></i> Tambah</button>
                    </div>
                    <div id="subtasks-container" class="mt-2 space-y-2"></div>
                </div>
            </form>

            <div id="ai-analyzer-section" class="border-t pt-4 mt-4 hidden">
                <h4 class="text-sm font-bold text-gray-800 mb-2">AI Todo Analyzer ✨</h4>
                <div id="ai-chat-history" class="h-48 overflow-y-auto p-3 bg-gray-50 rounded-lg border space-y-3 mb-2 flex flex-col"></div>
                <div class="flex items-center space-x-2">
                    <input type="text" id="ai-chat-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Minta AI untuk memecah tugas ini...">
                    <button type="button" id="ai-chat-send-button" class="p-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex-shrink-0">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end p-5 border-t border-gray-200 rounded-b gap-3">
            <button type="button" id="close-task-modal" class="px-5 py-2.5 text-gray-600 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium">
                Batal
            </button>
            <button type="button" id="save-task-button" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 text-white font-medium rounded-lg text-sm">
                <i class="fas fa-save mr-2"></i>Simpan Tugas
            </button>
        </div>
    </div>
</div>