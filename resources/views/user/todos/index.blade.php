<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Productivity Board') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header: Switch View + Tambah Tugas -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 px-4 sm:px-0">
                <div class="flex items-center space-x-1 p-1 bg-blue-50 rounded-xl backdrop-blur-sm shadow-sm">
                    <button id="view-switch-kanban" 
                        class="view-switch-button px-4 py-2 text-sm font-semibold rounded-lg transition-all flex items-center gap-2 active">
                        <i class="fas fa-grip-vertical"></i> Kanban
                    </button>
                    <button id="view-switch-eisenhower" 
                        class="view-switch-button px-4 py-2 text-sm font-semibold rounded-lg transition-all flex items-center gap-2">
                        <i class="fas fa-border-all"></i> Matriks
                    </button>
                </div>
                
                <button id="add-task-button" 
                    class="w-full sm:w-auto mt-4 sm:mt-0 bg-[#007bff] hover:bg-blue-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-md hover:shadow-lg transition-transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i> Tambah Tugas Baru
                </button>
            </div>

            <!-- Layout -->
            <div class="flex flex-col md:flex-row gap-6 lg:gap-8 px-4 sm:px-0">
                
                <!-- Sidebar Proyek -->
                <aside class="md:w-1/4 lg:w-1/5">
                    <div class="bg-white p-5 rounded-2xl shadow-md h-full flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-bold text-gray-800">Proyek</h2>
                            <button id="add-project-button" title="Tambah Proyek Baru" 
                                class="text-gray-400 hover:text-[#007bff] transition-colors">
                                <i class="fas fa-plus-circle fa-lg"></i>
                            </button>
                        </div>
                        <ul id="project-list" class="space-y-1 overflow-y-auto max-h-[70vh] pr-1">
                            <li>
                                <a href="{{ route('todos.index') }}" 
                                    class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ !$activeProjectId ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                    <i class="fas fa-inbox w-4 text-center"></i>
                                    <span>Semua Proyek</span>
                                </a>
                            </li>
                            @foreach ($projects as $project)
                                <li data-project-id="{{ $project->id }}" data-project-name="{{ e($project->name) }}">
                                    <div class="flex items-center group">
                                        <a href="{{ route('todos.index', ['project_id' => $project->id]) }}" 
                                            class="flex-1 flex justify-between items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ $activeProjectId == $project->id ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                            
                                            <div class="flex items-center gap-3 min-w-0"> 
                                                <i class="fas fa-folder w-4 text-center flex-shrink-0"></i> 
                                                <span class="truncate project-name">{{ $project->name }}</span> 
                                            </div>
                                            
                                            <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full text-xs font-semibold project-count flex-shrink-0">
                                                {{ $project->todos_count }}
                                            </span>
                                        </a>
                                        <div class="hidden group-hover:flex items-center ml-1">
                                            <button class="edit-project-button p-2 text-gray-400 hover:text-yellow-500 transition-colors" title="Ubah Proyek">
                                                <i class="fas fa-pencil fa-xs"></i>
                                            </button>
                                            <button class="delete-project-button p-2 text-gray-400 hover:text-red-500 transition-colors" title="Hapus Proyek">
                                                <i class="fas fa-trash-can fa-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                <!-- Main Content -->
                <div id="main-content-area" class="flex-1">
                    
                    <!-- Kanban View -->
                    <main id="kanban-view" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 view-container">
                        @php
                            $statuses = [
                                'todo' => ['name' => 'Tugas Baru', 'color' => 'border-blue-500'], 
                                'in_progress' => ['name' => 'Sedang Dikerjakan', 'color' => 'border-yellow-500'], 
                                'done' => ['name' => 'Selesai', 'color' => 'border-green-500']
                            ];
                        @endphp
                        @foreach ($statuses as $statusKey => $statusData)
                            <div class="bg-white/80 rounded-2xl shadow-sm kanban-column drop-zone flex flex-col border border-gray-100">
                                <div class="flex justify-between items-center mb-3 p-4 border-b {{ $statusData['color'] }}">
                                    <h3 class="text-sm font-semibold text-gray-700">{{ $statusData['name'] }}</h3>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-bold task-count">
                                        {{ $tasks->where('status', $statusKey)->count() }}
                                    </span>
                                </div>
                                <div class="card-container space-y-3 flex-1 min-h-[10rem] p-4 overflow-y-auto">
                                    @forelse ($tasks->where('status', $statusKey) as $task)
                                        @include('user.todos.partials._task-card', ['task' => $task, 'interruptedSession' => $interruptedSession])
                                    @empty
                                        <div class="empty-state-message text-center text-sm text-gray-500 py-4">Seret tugas ke sini.</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </main>

                    <!-- Eisenhower View -->
                    <main id="eisenhower-view" class="hidden view-container">
                        @php
                            $quadrants = [
                                'do' => ['title' => 'Penting & Mendesak', 'subtitle' => 'Lakukan Segera', 'colors' => 'bg-red-50 border-red-200'],
                                'schedule' => ['title' => 'Penting & Tidak Mendesak', 'subtitle' => 'Jadwalkan', 'colors' => 'bg-blue-50 border-blue-200'],
                                'delegate' => ['title' => 'Mendesak & Tidak Penting', 'subtitle' => 'Delegasikan', 'colors' => 'bg-yellow-50 border-yellow-200'],
                                'delete' => ['title' => 'Tidak Mendesak & Tidak Penting', 'subtitle' => 'Eliminasi', 'colors' => 'bg-gray-100 border-gray-200'],
                            ];
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 grid-rows-2 gap-4 h-[75vh]">
                            @foreach ($quadrants as $quadrantKey => $quadrantData)
                                <div class="border rounded-2xl p-4 matrix-quadrant drop-zone flex flex-col {{ $quadrantData['colors'] }} shadow-sm">
                                    <div class="mb-3">
                                        <h4 class="font-semibold text-gray-800">{{ $quadrantData['title'] }}</h4>
                                        <p class="text-xs text-gray-500">{{ $quadrantData['subtitle'] }}</p>
                                    </div>
                                    <div class="card-container space-y-2 flex-1 h-full overflow-y-auto pr-1">
                                        @forelse ($tasks->where('eisenhower_quadrant', $quadrantKey) as $task)
                                            @include('user.todos.partials._task-card', ['task' => $task, 'interruptedSession' => $interruptedSession])
                                        @empty
                                            <div class="empty-state-message text-center text-xs text-gray-400 pt-4">Letakkan tugas di sini.</div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    @include('user.todos.partials.project-modal')
    @include('user.todos.partials.task-modal')

    {{-- Modal untuk Konfirmasi Sub-tugas --}}
    <div id="subtask-confirmation-modal" 
        class="fixed inset-0 z-50 hidden bg-gray-600 bg-opacity-50 flex items-center justify-center px-4 sm:px-0">

        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-6 transform transition-all">
            <!-- Header -->
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-yellow-500"></i>
                Konfirmasi
            </h3>

            <!-- Body -->
            <div class="mt-3">
                <p class="text-sm text-gray-600">
                    Ada sub-task yang belum kamu selesaikan. Apakah kamu ingin tetap melanjutkan?
                </p>
            </div>

            <!-- Footer -->
            <div class="mt-6 flex flex-col-reverse sm:flex-row gap-3">
                <button type="button" id="confirm-cancel-button"
                    class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 transition">
                    Nanti Dulu
                </button>

                <button type="button" id="confirm-proceed-button"
                    class="w-full sm:w-auto inline-flex justify-center rounded-lg px-4 py-2 text-sm font-semibold text-white bg-[#007bff] hover:bg-blue-600 shadow-md transition">
                    Tetap Lanjut
                </button>
            </div>
        </div>
    </div>

    {{-- Widget Timer Pomodoro --}}
    <div id="pomodoro-widget" 
        class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md p-4 mb-4 bg-[#007bff] text-white rounded-t-2xl shadow-2xl hidden transition-all duration-300">

        <div class="flex items-center justify-between">
            <!-- Info Sesi -->
            <div>
                <p id="pomodoro-session-type" class="text-xs uppercase tracking-wide text-blue-100">Focusing On:</p>
                <p id="pomodoro-task-title" class="font-semibold truncate">-</p>
            </div>

            <!-- Timer & Controls -->
            <div class="text-right">
                <p id="pomodoro-timer" class="text-3xl font-mono font-bold">25:00</p>
                <div class="flex items-center justify-end gap-2 mt-2">
                    {{-- Tombol Pause --}}
                    <button id="pomodoro-interrupt-button" 
                            class="px-3 py-1.5 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-sm font-medium shadow-md transition">
                        <i class="fas fa-pause mr-1"></i> Pause
                    </button>
                    {{-- Tombol Stop --}}
                    <button id="pomodoro-stop-button" 
                            class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-sm font-medium shadow-md transition">
                        <i class="fas fa-stop mr-1"></i> Stop
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // --- 1. PROJECT CRUD ---
                const projectModule = (() => {
                    const projectModal = document.getElementById('project-modal');
                    const projectModalTitle = document.getElementById('project-modal-title');
                    const projectForm = document.getElementById('project-form');
                    const projectIdInput = document.getElementById('project-id');
                    const projectNameInput = document.getElementById('project-name-input');
                    
                    const showModal = (title, project = null) => {
                        projectModalTitle.textContent = title;
                        projectForm.reset();
                        projectIdInput.value = project ? project.id : '';
                        projectNameInput.value = project ? project.name : '';
                        projectModal.classList.remove('hidden');
                    };
                    const hideModal = () => projectModal.classList.add('hidden');

                    const handleFormSubmit = async (e) => {
                        e.preventDefault();
                        const projectId = projectIdInput.value;
                        const url = projectId ? `/api/projects/${projectId}` : '/api/projects';
                        const method = projectId ? 'PUT' : 'POST';
                        try {
                            const response = await fetch(url, {
                                method: method,
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                                body: JSON.stringify({ name: projectNameInput.value })
                            });
                            if (!response.ok) throw new Error('Failed to save project.');
                            window.location.reload();
                        } catch (error) { alert(`Error: ${error.message}`); }
                    };

                    const handleListClick = async (e) => {
                        const editButton = e.target.closest('.edit-project-button');
                        const deleteButton = e.target.closest('.delete-project-button');
                        const projectItem = e.target.closest('li[data-project-id]');
                        if (!projectItem) return;

                        if (editButton) {
                            const project = { id: projectItem.dataset.projectId, name: projectItem.dataset.projectName };
                            showModal('Edit Project', project);
                        }
                        if (deleteButton) {
                            if (confirm('Are you sure you want to delete this project and all its tasks?')) {
                                try {
                                    const response = await fetch(`/api/projects/${projectItem.dataset.projectId}`, {
                                        method: 'DELETE',
                                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
                                    });
                                    if (!response.ok) throw new Error('Failed to delete project.');
                                    projectItem.remove();
                                } catch (error) { alert(`Error: ${error.message}`); }
                            }
                        }
                    };

                    document.getElementById('add-project-button').addEventListener('click', () => showModal('Add New Project'));
                    document.getElementById('close-project-modal').addEventListener('click', hideModal);
                    projectForm.addEventListener('submit', handleFormSubmit);
                    document.getElementById('project-list').addEventListener('click', handleListClick);
                })();


                // --- 2. TASK CRUD ---
                const taskModule = (() => {
                    const taskModal = document.getElementById('task-modal');
                    const taskModalTitle = document.getElementById('task-modal-title');
                    const taskForm = document.getElementById('task-form');
                    const taskIdInput = document.getElementById('task-id');
                    const subtasksContainer = document.getElementById('subtasks-container');

                    // Listener untuk tombol "Batal"
                    document.getElementById('close-task-modal').addEventListener('click', hideModal);
                    
                    // [TAMBAHKAN INI] Listener untuk tombol 'X' di pojok kanan atas
                    document.getElementById('close-task-modal-x').addEventListener('click', hideModal);

                    const showModal = async (title, taskId = null) => {
                        // 1. Setup awal modal
                        taskModalTitle.textContent = title;
                        taskForm.reset();
                        taskIdInput.value = '';
                        subtasksContainer.innerHTML = ''; // Selalu bersihkan sub-tugas lama

                        // 2. Cek apakah ini mode "Edit" atau "Tambah"
                        if (taskId) {
                            // --- INI ADALAH LOGIKA MODE EDIT ANDA ---
                            try {
                                // Ambil detail tugas dari API
                                const response = await fetch(`/api/todos/${taskId}`);
                                if (!response.ok) throw new Error('Failed to fetch task details.');
                                const task = await response.json();
                                
                                // Isi semua field form dengan data yang ada
                                document.getElementById('task-id').value = task.id;
                                document.getElementById('task-title').value = task.title || '';
                                document.getElementById('task-notes').value = task.notes || '';
                                document.getElementById('task-project').value = task.project_id || '';
                                document.getElementById('task-priority').value = task.priority || 'medium';
                                document.getElementById('task-eisenhower').value = task.eisenhower_quadrant || '';
                                document.getElementById('task-duration').value = task.pomodoro_custom_duration || '';
                                if (task.due_date) {
                                    const date = new Date(task.due_date);
                                    const localDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                                    document.getElementById('task-due-date').value = localDate;
                                }
                                if (task.subtasks) {
                                    task.subtasks.forEach(st => addSubtaskRow(st));
                                }
                            } catch (error) { 
                                alert(`Error: ${error.message}`); 
                                return; // Hentikan jika gagal mengambil data
                            }
                            
                            // --- INI ADALAH PENAMBAHAN LOGIKA BARU UNTUK AI ---
                            // Tampilkan bagian AI dan muat riwayat chat-nya
                            aiAnalyzerModule.show();
                            aiAnalyzerModule.fetchAndRenderHistory(taskId);

                        } else {
                            // --- INI ADALAH LOGIKA MODE TAMBAH ---
                            // Sembunyikan bagian AI karena ini tugas baru
                            document.getElementById('ai-analyzer-section').classList.add('hidden');
                        }

                        // 3. Setelah semuanya siap, tampilkan modal
                        taskModal.classList.remove('hidden');
                    };

                    const hideModal = () => taskModal.classList.add('hidden');
                    
                    const addSubtaskRow = (subtask = {id: '', title: '', status: 'todo'}) => {
                        const row = document.createElement('div');
                        row.className = 'flex items-center gap-2';
                        row.innerHTML = `
                            <input type="hidden" class="subtask-id" value="${subtask.id || ''}">
                            <input type="checkbox" class="rounded subtask-status" ${subtask.status === 'done' ? 'checked' : ''}>
                            <input type="text" class="block w-full input-style text-sm subtask-title" placeholder="Sub-task description..." value="${subtask.title || ''}">
                            <button type="button" class="remove-subtask-button p-1 text-gray-400 hover:text-red-600">
                                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        `;
                        subtasksContainer.appendChild(row);
                    };

                    const saveTask = async () => {
                        const taskId = taskIdInput.value;
                        const subtasks = [];
                        document.querySelectorAll('#subtasks-container .flex').forEach(row => {
                            subtasks.push({
                                id: row.querySelector('.subtask-id').value || null,
                                title: row.querySelector('.subtask-title').value,
                                status: row.querySelector('.subtask-status').checked ? 'done' : 'todo'
                            });
                        });

                        const formData = new FormData(taskForm);
                        const data = Object.fromEntries(formData.entries());
                        data.subtasks = subtasks;
                        for (const key in data) {
                            if (data[key] === '' || data[key] === null) delete data[key];
                        }

                        const url = taskId ? `/api/todos/${taskId}` : '/api/todos';
                        const method = taskId ? 'PUT' : 'POST';

                        try {
                            const response = await fetch(url, {
                                method: method,
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                                body: JSON.stringify(data)
                            });
                            if (!response.ok) { const error = await response.json(); throw new Error(error.message || 'Failed to save task.'); }
                            window.location.reload();
                        } catch (error) { alert(`Error: ${error.message}`); }
                    };

                    const deleteTask = (taskId) => {
                        if (confirm('Are you sure you want to delete this task?')) {
                            fetch(`/api/todos/${taskId}`, {
                                method: 'DELETE',
                                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
                            }).then(res => {
                                if (!res.ok) throw new Error('Failed to delete.');
                                document.querySelector(`.task-card[data-task-id="${taskId}"]`).remove();
                                // Anda mungkin perlu memanggil fungsi update UI kolom di sini
                            }).catch(err => alert(`Error: ${err.message}`));
                        }
                    };

                    document.getElementById('add-task-button').addEventListener('click', () => showModal('Add New Task'));
                    document.getElementById('close-task-modal').addEventListener('click', hideModal);
                    document.getElementById('add-subtask-button').addEventListener('click', () => addSubtaskRow());
                    subtasksContainer.addEventListener('click', e => {
                        if (e.target.closest('.remove-subtask-button')) e.target.closest('.flex').remove();
                    });
                    document.getElementById('save-task-button').addEventListener('click', saveTask);
                    document.querySelector('.flex-1').addEventListener('click', (e) => {
                        const taskCard = e.target.closest('.task-card');
                        if (!taskCard) return;
                        const taskId = taskCard.dataset.taskId;
                        if (e.target.closest('.delete-task-button')) { deleteTask(taskId); } 
                        else if (e.target.closest('.edit-task-trigger')) { showModal('Edit Task', taskId); }
                    });
                    return { showModal, deleteTask };
                })();


                // --- 3. VIEW SWITCHER ---
                const viewSwitcherModule = (() => {
                    const kanbanView = document.getElementById('kanban-view');
                    const eisenhowerView = document.getElementById('eisenhower-view');
                    const kanbanButton = document.getElementById('view-switch-kanban');
                    const eisenhowerButton = document.getElementById('view-switch-eisenhower');
                    
                    const switchToKanban = () => {
                        kanbanView.classList.remove('hidden');
                        eisenhowerView.classList.add('hidden');
                        kanbanButton.classList.add('active');
                        eisenhowerButton.classList.remove('active');
                    };
                    const switchToEisenhower = () => {
                        kanbanView.classList.add('hidden');
                        eisenhowerView.classList.remove('hidden');
                        kanbanButton.classList.remove('active');
                        eisenhowerButton.classList.add('active');
                    };
                    kanbanButton.addEventListener('click', switchToKanban);
                    eisenhowerButton.addEventListener('click', switchToEisenhower);
                })();

                // --- 4. DRAG & DROP ---
                const dragDropModule = (() => {
                    const dropZones = document.querySelectorAll('.drop-zone');
                    
                    const updateDropZoneUI = (zone) => {
                        if (!zone) return;
                        const cardContainer = zone.querySelector('.card-container');
                        const taskCountElement = zone.querySelector('.task-count');
                        let emptyStateMessage = zone.querySelector('.empty-state-message');
                        const taskCount = cardContainer.querySelectorAll('.task-card').length;
                        if (taskCountElement) {
                            taskCountElement.textContent = taskCount;
                        }
                        if (taskCount > 0 && emptyStateMessage) {
                            emptyStateMessage.remove();
                        } else if (taskCount === 0 && !emptyStateMessage) {
                            const newEmptyState = document.createElement('div');
                            newEmptyState.className = 'empty-state-message text-center text-sm text-gray-500 py-4';
                            newEmptyState.textContent = zone.dataset.type === 'status' ? 'No tasks here.' : 'Drop tasks here.';
                            cardContainer.appendChild(newEmptyState);
                        }
                    };
                    
                    const initDraggableCards = () => {
                        document.querySelectorAll('.task-card').forEach(card => {
                            card.addEventListener('dragstart', (e) => {
                                e.dataTransfer.setData('text/plain', e.target.dataset.taskId);
                                setTimeout(() => e.target.classList.add('dragging'), 0);
                            });
                            card.addEventListener('dragend', (e) => {
                                e.target.classList.remove('dragging');
                            });
                        });
                    };
                    
                    dropZones.forEach(zone => {
                        zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('drag-over'); });
                        zone.addEventListener('dragleave', () => { zone.classList.remove('drag-over'); });
                        zone.addEventListener('drop', async (e) => {
                            e.preventDefault();
                            zone.classList.remove('drag-over');
                            const taskId = e.dataTransfer.getData('text/plain');
                            const draggedCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
                            if (!draggedCard) return;
                            const sourceZone = draggedCard.closest('.drop-zone');
                            zone.querySelector('.card-container').appendChild(draggedCard);
                            updateDropZoneUI(sourceZone);
                            updateDropZoneUI(zone);
                            const dropType = zone.dataset.type;
                            const dropValue = zone.dataset.value;
                            let url = '';
                            let body = {};
                            if (dropType === 'status') {
                                url = `/api/todos/${taskId}/status`;
                                body = { status: dropValue };
                            } else if (dropType === 'quadrant') {
                                url = `/api/todos/${taskId}/quadrant`;
                                body = { quadrant: dropValue };
                                draggedCard.dataset.quadrant = dropValue;
                            } else { return; }
                            try {
                                const response = await fetch(url, {
                                    method: 'PATCH',
                                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                                    body: JSON.stringify(body)
                                });
                                if (!response.ok) throw new Error(`Failed to update task ${dropType}.`);
                            } catch (error) {
                                alert('Error: ' + error.message);
                                sourceZone.querySelector('.card-container').appendChild(draggedCard);
                                updateDropZoneUI(sourceZone);
                                updateDropZoneUI(zone);
                            }
                        });
                    });

                    initDraggableCards();
                    dropZones.forEach(updateDropZoneUI);
                    
                    // Expose a function to be called from other modules
                    return { updateDropZoneUI };
                })();

                // --- 5. POMODORO MODULE (LOGIKA LENGKAP) ---
                const pomodoroModule = (() => {
                    const widget = document.getElementById('pomodoro-widget');
                    const timerDisplay = document.getElementById('pomodoro-timer');
                    const taskTitleDisplay = document.getElementById('pomodoro-task-title');
                    const sessionTypeDisplay = document.getElementById('pomodoro-session-type');
                    const interruptButton = document.getElementById('pomodoro-interrupt-button');
                    const finishSound = document.getElementById('pomodoro-finish-sound');
                    const confirmationModal = document.getElementById('subtask-confirmation-modal');

                    let activeTimerInterval = null;
                    let sessionQueue = []; // Antrian untuk sesi kerja -> istirahat
                    let currentSession = null;
                    let currentTaskInfo = { id: null, card: null };
                    let remainingSeconds = 0;

                    // [BARU] Fungsi helper untuk mengubah tombol Pomodoro pada kartu secara dinamis
                    const swapPomodoroButton = (taskId, toState, sessionId = null) => {
                        const taskCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
                        if (!taskCard) return;

                        const buttonContainer = taskCard.querySelector('.group-hover\\:flex');
                        if (!buttonContainer) return;

                        // Hapus tombol pomodoro yang ada
                        const existingStart = buttonContainer.querySelector('.start-pomodoro-button');
                        const existingResume = buttonContainer.querySelector('.resume-pomodoro-button');
                        if (existingStart) existingStart.remove();
                        if (existingResume) existingResume.remove();

                        let newButtonHTML = '';
                        if (toState === 'resume') {
                            newButtonHTML = `
                                <button class="resume-pomodoro-button p-1 text-gray-400 hover:text-blue-600" title="Resume Pomodoro Session" data-session-id="${sessionId}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                </button>
                            `;
                        } else { // 'start'
                            newButtonHTML = `
                                <button class="start-pomodoro-button p-1 text-gray-400 hover:text-green-600" title="Start Pomodoro Session">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg>
                                </button>
                            `;
                        }
                        // Masukkan tombol baru di awal grup tombol
                        buttonContainer.insertAdjacentHTML('afterbegin', newButtonHTML);
                    };

                    const formatTime = (seconds) => {
                        const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
                        const secs = (seconds % 60).toString().padStart(2, '0');
                        return `${mins}:${secs}`;
                    };

                    const updateSessionStatus = async (session, status) => {
                        const body = { status: status };
                        if (status === 'interrupted') {
                            body.remaining_seconds = remainingSeconds;
                        }
                        try {
                            await fetch(`/api/pomodoro-sessions/${session.id}`, {
                                method: 'PATCH',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                                body: JSON.stringify(body)
                            });
                        } catch (error) {
                            console.error('Failed to update session status:', error);
                        }
                    };
                    
                    const completeTask = async () => {
                        if (!currentTaskInfo.id) return;
                        
                        try {
                            // 1. Update status tugas menjadi 'done'
                            await fetch(`/api/todos/${currentTaskInfo.id}/status`, {
                                method: 'PATCH',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                                body: JSON.stringify({ status: 'done' })
                            });

                            // 2. Pindahkan kartu ke kolom "Done"
                            const sourceZone = currentTaskInfo.card.closest('.drop-zone');
                            const doneZone = document.querySelector('.kanban-column[data-value="done"]');
                            if (doneZone) {
                                doneZone.querySelector('.card-container').appendChild(currentTaskInfo.card);
                                dragDropModule.updateDropZoneUI(sourceZone);
                                dragDropModule.updateDropZoneUI(doneZone);
                            }
                            
                        } catch(error) {
                            console.error('Failed to complete task:', error);
                            alert('Error completing the task. Please move it to Done manually.');
                        }

                        // [BARU] Kembalikan tombol ke 'Start' setelah tugas selesai
                        swapPomodoroButton(currentTaskInfo.id, 'start');
                        
                        widget.classList.add('hidden');
                        document.title = "{{ config('app.name', 'RelaxBoss') }}";
                        currentTaskInfo = { id: null, card: null };
                    };

                    const runNextSessionInQueue = () => {
                        if (sessionQueue.length > 0) {
                            currentSession = sessionQueue.shift(); // Ambil sesi berikutnya dari antrian
                            runTimer(currentSession, currentTaskInfo.card.querySelector('p').textContent);
                        } else {
                            // Jika tidak ada sesi lagi di antrian, selesaikan tugas
                            completeTask();
                        }
                    };

                    const runTimer = (session, taskTitle, resumeFromSeconds = null) => {
                        widget.classList.remove('hidden');
                        
                        // Sesuaikan UI berdasarkan tipe sesi
                        if (session.type === 'short_break') {
                            widget.classList.add('break-mode');
                            sessionTypeDisplay.textContent = 'BREAK TIME:';
                            taskTitleDisplay.textContent = 'Time to relax!';
                        } else {
                            widget.classList.remove('break-mode');
                            sessionTypeDisplay.textContent = 'FOCUSING ON:';
                            taskTitleDisplay.textContent = taskTitle;
                        }

                        remainingSeconds = resumeFromSeconds ?? session.duration_minutes * 60;

                        activeTimerInterval = setInterval(() => {
                            remainingSeconds--;
                            timerDisplay.textContent = formatTime(remainingSeconds);
                            document.title = `${formatTime(remainingSeconds)} - ${taskTitle}`;

                            if (remainingSeconds <= 0) {
                                clearInterval(activeTimerInterval);
                                activeTimerInterval = null;
                                
                                if(finishSound) finishSound.play();
                                updateSessionStatus(session, 'completed');
                                runNextSessionInQueue();
                            }
                        }, 1000);
                    };

                    const startPomodoroApiCall = async (taskId, taskCard, forceComplete) => {
                        try {
                            const response = await fetch(`/api/todos/${taskId}/pomodoro`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                                body: JSON.stringify({ force_complete_subtasks: forceComplete })
                            });

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.message || 'Failed to start Pomodoro session.');
                            }
                            const { sessions } = await response.json();
                            
                            if (sessions && sessions.length > 0) {
                                sessionQueue = sessions;
                                currentTaskInfo = { id: taskId, card: taskCard };
                                
                                const sourceZone = taskCard.closest('.drop-zone');
                                const inProgressZone = document.querySelector('.kanban-column[data-value="in_progress"]');
                                if (inProgressZone && sourceZone.dataset.value !== 'in_progress') {
                                    inProgressZone.querySelector('.card-container').appendChild(taskCard);
                                    dragDropModule.updateDropZoneUI(sourceZone);
                                    dragDropModule.updateDropZoneUI(inProgressZone);
                                }

                                runNextSessionInQueue();
                            }
                        } catch(error) {
                            console.error('Pomodoro Error:', error);
                            alert('Error: ' + error.message);
                        }
                    };

                    const startSession = async (taskId, taskCard) => {
                        if (activeTimerInterval) {
                            alert('Another Pomodoro session is already running.');
                            return;
                        }

                        try {
                            // Langkah 1: Ambil detail tugas untuk memeriksa sub-tugas
                            const taskDetailsResponse = await fetch(`/api/todos/${taskId}`);
                            if (!taskDetailsResponse.ok) throw new Error('Could not fetch task details.');
                            const task = await taskDetailsResponse.json();

                            const hasUncompletedSubtasks = task.subtasks && task.subtasks.some(st => st.status !== 'done');

                            // Langkah 2: Logika Kondisional
                            if (hasUncompletedSubtasks) {
                                // Tampilkan modal konfirmasi
                                confirmationModal.classList.remove('hidden');

                                // Tunggu pilihan pengguna
                                const userChoice = await new Promise(resolve => {
                                    document.getElementById('confirm-proceed-button').onclick = () => resolve('proceed');
                                    document.getElementById('confirm-cancel-button').onclick = () => resolve('cancel');
                                });

                                confirmationModal.classList.add('hidden'); // Sembunyikan modal setelah memilih

                                if (userChoice === 'cancel') {
                                    return; // Hentikan proses
                                }
                                
                                // Jika pengguna memilih "Tetap Lanjut", panggil API dengan flag `true`
                                await startPomodoroApiCall(taskId, taskCard, true);

                            } else {
                                // Jika tidak ada sub-tugas yang belum selesai, lanjutkan seperti biasa
                                await startPomodoroApiCall(taskId, taskCard, false);
                            }

                        } catch (error) {
                            console.error('Pomodoro Start Error:', error);
                            alert('Error: ' + error.message);
                        }
                    };

                    const resumeSession = async (sessionId, taskCard) => {
                        if (activeTimerInterval) {
                            alert('Another Pomodoro session is already running.');
                            return;
                        }
                        try {
                            // [PERBAIKAN] Menambahkan header yang benar ke request fetch
                            const response = await fetch(`/api/pomodoro-sessions/${sessionId}/resume`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': getCsrfToken()
                                }
                            });
                            if (!response.ok) throw new Error('Failed to resume session.');
                            
                            const session = await response.json();

                            // [BARU] Ubah tombol 'Resume' kembali menjadi 'Start' (secara visual, karena akan segera disembunyikan oleh hover)
                            swapPomodoroButton(session.todo_id, 'start'); 

                            sessionQueue = []; // Tidak ada sesi istirahat saat resume
                            currentTaskInfo = { id: session.todo_id, card: taskCard };
                            
                            runTimer(session, taskCard.querySelector('p').textContent, session.remaining_seconds);

                        } catch (error) {
                            alert('Error: ' + error.message);
                        }
                    };
                    
                    const interruptTimer = () => {
                        if (!activeTimerInterval || !currentSession) return;
                        
                        clearInterval(activeTimerInterval);
                        activeTimerInterval = null;
                        widget.classList.add('hidden');
                        document.title = "{{ config('app.name', 'RelaxBoss') }}";
                        
                        updateSessionStatus(currentSession, 'interrupted');

                        // [BARU] Ubah tombol 'Start' menjadi 'Resume' secara dinamis
                        swapPomodoroButton(currentSession.todo_id, 'resume', currentSession.id);

                        sessionQueue = [];
                        currentSession = null;
                        
                        alert('Session paused.');
                    };

                    interruptButton.addEventListener('click', interruptTimer);

                    return { startSession, resumeSession };
                })();

                // --- [DIUBAH] 6. MASTER EVENT LISTENER ---
                const mainContentArea = document.querySelector('#main-content-area');
                mainContentArea.addEventListener('click', (event) => {
                    const taskCard = event.target.closest('.task-card');
                    if (!taskCard) return;

                    const taskId = taskCard.dataset.taskId;
                    const startButton = event.target.closest('.start-pomodoro-button');
                    const resumeButton = event.target.closest('.resume-pomodoro-button');
                    const deleteButton = event.target.closest('.delete-task-button');

                    if (startButton) {
                        pomodoroModule.startSession(taskId, taskCard);
                    } else if (resumeButton) {
                        pomodoroModule.resumeSession(resumeButton.dataset.sessionId, taskCard);
                    } else if (deleteButton) {
                        taskModule.deleteTask(taskId, taskCard);
                    } else {
                        taskModule.showModal('Edit Task', taskId);
                    }
                });

                // --- [BARU] 7. AI ANALYZER MODULE ---
                const aiAnalyzerModule = (() => {
                    const section = document.getElementById('ai-analyzer-section');
                    const historyContainer = document.getElementById('ai-chat-history');
                    const input = document.getElementById('ai-chat-input');
                    const sendButton = document.getElementById('ai-chat-send-button');

                    const renderMessage = (message) => {
                        const bubble = document.createElement('div');
                        bubble.className = `chat-bubble ${message.sender_type}`;
                        bubble.textContent = message.message_text;
                        historyContainer.appendChild(bubble);
                    };

                    const fetchAndRenderHistory = async (taskId) => {
                        historyContainer.innerHTML = ''; // Kosongkan riwayat lama
                        try {
                            const response = await fetch(`/api/todos/${taskId}/ai-messages`);
                            if (!response.ok) throw new Error('Failed to load chat history.');
                            const messages = await response.json();
                            messages.forEach(renderMessage);
                            historyContainer.scrollTop = historyContainer.scrollHeight; // Scroll ke bawah
                        } catch (error) {
                            console.error('AI History Error:', error);
                        }
                    };
                    
                    const handleSendMessage = async () => {
                        const taskId = document.getElementById('task-id').value;
                        const messageText = input.value.trim();
                        if (!taskId || !messageText) return;

                        // Tampilkan pesan pengguna secara instan
                        renderMessage({ sender_type: 'user', message_text: messageText });
                        input.value = '';
                        historyContainer.scrollTop = historyContainer.scrollHeight;

                        try {
                            const response = await fetch(`/api/todos/${taskId}/ai-messages`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                                body: JSON.stringify({ message: messageText })
                            });
                            if (!response.ok) throw new Error('AI failed to respond.');
                            const { reply } = await response.json();
                            renderMessage(reply); // Tampilkan balasan AI
                            historyContainer.scrollTop = historyContainer.scrollHeight;
                        } catch (error) {
                            renderMessage({ sender_type: 'ai', message_text: 'Sorry, an error occurred.' });
                        }
                    };
                    
                    // Panggil handleSendMessage saat tombol di-klik atau tombol Enter ditekan
                    sendButton.addEventListener('click', handleSendMessage);
                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            handleSendMessage();
                        }
                    });

                    return { fetchAndRenderHistory, show: () => section.classList.remove('hidden') };
                })();
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Drag state */
            .task-card.dragging {
                @apply opacity-50 scale-105 shadow-2xl;
            }

            /* Drop zone highlight */
            .drop-zone.drag-over {
                @apply bg-blue-100 border-2 border-dashed border-blue-500;
            }

            /* Tombol switch view (Kanban/Matrix) */
            .view-switch-button {
                @apply text-gray-600 hover:bg-blue-100 hover:text-blue-600;
            }
            .view-switch-button.active {
                background-color: #007bff;
                @apply text-white shadow-md;
            }

            /* Pomodoro break mode */
            #pomodoro-widget.break-mode {
                @apply bg-green-500;
            }

            /* Truncate helper */
            .truncate {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            #pomodoro-widget.break-mode {
                @apply bg-green-500;
            }
        </style>
    @endpush

</x-app-layout>

