export function todoApp(initialTasks = {}, initialProjects = []) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end", // Posisi di pojok kanan atas
        showConfirmButton: false,
        timer: 3000, // Hilang dalam 3 detik
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });
    return {
        // ===================================================================
        // === STATE (Data Storage & UI Status)
        // ===================================================================

        tasks: initialTasks,
        projects: initialProjects,
        viewMode: "kanban",
        isLoading: false, // Modal states
        isTaskModalOpen: false,
        isEditMode: false,
        editingTask: null,
        isProjectModalOpen: false,
        editingProjectId: null,
        taskForm: {
            task: "",
            project_id: "",
            notes: "",
            due_date: "",
            priority: "medium",
            eisenhower_quadrant: "",
            file: null,
            subtasks: [],
            pomodoro_custom_duration: null,
        },

        projectForm: {
            name: "",
            description: "",
        },

        pomodoroDurations: { work: 25, short_break: 5, long_break: 15 },
        timeLeft: 0,
        timerInterval: null,
        isRunning: this.$persist(false).as("pomodoro_is_running"),
        pomodoroTaskName: this.$persist("Tugas Umum").as("pomodoro_task_name"),
        pomodoroTaskId: this.$persist(null).as("pomodoro_task_id"),
        activePomodoroSessionId: this.$persist(null).as("pomodoro_session_id"),
        pomodoroCycle: this.$persist(0).as("pomodoro_cycle"),
        lastWorkDuration: this.$persist(25).as("pomodoro_last_work_duration"),
        pomodoroEndTime: this.$persist(null).as("pomodoro_end_time"),
        isPomodoroActive: this.$persist(false).as("pomodoro_is_active"),

        // --- State Baru untuk Integrasi AI ---
        isAnalyzingAi: false,
        showAiSuggestions: false,
        aiSuggestionsData: {
            // Menambahkan suggested_project_name dan suggested_eisenhower_quadrant
            suggested_project_name: null,
            main_task: "", // Mengganti task_analysis menjadi main_task
            suggested_subtasks: [],
            suggested_priority: "medium",
            suggested_eisenhower_quadrant: null,
        },

        // ===================================================================
        // === INITIALIZATION
        // ===================================================================
        init() {
            const wrapper = document.querySelector("#todo-app-wrapper");
            const indexUrl = wrapper.dataset.indexUrl;

            this.listenForStorageChanges();
            this.isLoading = true;
            this.updateUiState();

            fetch(indexUrl, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    this.tasks = data;
                    this.restorePausedSessionFromData(data);
                })
                .catch((error) => {
                    console.error("Gagal memuat data tugas:", error);

                    // [PERUBAHAN] Menggunakan SweetAlert untuk notifikasi error
                    Swal.fire({
                        icon: "error",
                        title: "Gagal Memuat Data",
                        text: "Terjadi masalah saat mengambil data dari server. Silakan muat ulang halaman.",
                        confirmButtonColor: "#007BFF",
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        updateUiState() {
            const analyzeAiButton = document.getElementById("analyzeAiButton");
            const aiSuggestionsArea =
                document.getElementById("aiSuggestionsArea");
            const aiSuggestedProjectName = document.getElementById(
                "aiSuggestedProjectName"
            ); // New element
            const aiTaskAnalysis = document.getElementById("aiTaskAnalysis");
            const aiSuggestedPriority = document.getElementById(
                "aiSuggestedPriority"
            );
            const aiSuggestedEisenhowerQuadrant = document.getElementById(
                "aiSuggestedEisenhowerQuadrant"
            ); // New element
            const aiSuggestedSubtasks = document.getElementById(
                "aiSuggestedSubtasks"
            );

            if (analyzeAiButton) {
                analyzeAiButton.disabled = this.isAnalyzingAi;
                analyzeAiButton.textContent = this.isAnalyzingAi
                    ? "Menganalisis..."
                    : "Analisis AI";
            }

            if (aiSuggestionsArea) {
                if (this.showAiSuggestions) {
                    aiSuggestionsArea.classList.remove("hidden");
                } else {
                    aiSuggestionsArea.classList.add("hidden");
                }
            }

            // Update konten area saran
            if (aiSuggestedProjectName) {
                aiSuggestedProjectName.textContent =
                    this.aiSuggestionsData.suggested_project_name || "";
            }
            if (aiTaskAnalysis) {
                aiTaskAnalysis.textContent =
                    this.aiSuggestionsData.main_task ||
                    "Tidak ada analisis spesifik.";
            }
            if (aiSuggestedPriority) {
                aiSuggestedPriority.textContent =
                    this.aiSuggestionsData.suggested_priority ||
                    "Tidak disarankan";
            }
            if (aiSuggestedEisenhowerQuadrant) {
                aiSuggestedEisenhowerQuadrant.textContent =
                    this.aiSuggestionsData.suggested_eisenhower_quadrant || "";
            }

            if (aiSuggestedSubtasks) {
                aiSuggestedSubtasks.innerHTML = "";
                if (
                    this.aiSuggestionsData.suggested_subtasks &&
                    Array.isArray(this.aiSuggestionsData.suggested_subtasks)
                ) {
                    this.aiSuggestionsData.suggested_subtasks.forEach(
                        (subtask) => {
                            const li = document.createElement("li");
                            li.textContent = subtask;
                            aiSuggestedSubtasks.appendChild(li);
                        }
                    );
                } else {
                    const li = document.createElement("li");
                    li.textContent = "Tidak ada sub-tugas yang disarankan.";
                    aiSuggestedSubtasks.appendChild(li);
                }
            }
        },

        async analyzeTaskWithAI() {
            const taskInput = document.getElementById("taskInput");
            const taskText = taskInput ? taskInput.value : "";

            if (!taskText.trim()) {
                Toast.fire({
                    icon: "warning",
                    title: "Silakan masukkan teks tugas untuk dianalisis.",
                });
                return;
            }

            this.isAnalyzingAi = true;
            this.showAiSuggestions = false;
            this.updateUiState();

            try {
                const response = await fetch("api/todos/analyze-with-ai", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify({
                        task_text: taskText,
                    }),
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(
                        errorData.message ||
                            "Gagal menganalisis tugas dengan AI."
                    );
                }

                const data = await response.json();
                console.log("Respons AI:", data);

                if (data.success) {
                    try {
                        const aiSuggestions = JSON.parse(
                            data.ai_parsed_response
                        );

                        this.aiSuggestionsData = {
                            suggested_project_name:
                                aiSuggestions.suggested_project_name || null,
                            main_task:
                                aiSuggestions.main_task || taskText.trim(),
                            suggested_subtasks:
                                aiSuggestions.suggested_subtasks || [],
                            suggested_priority:
                                aiSuggestions.suggested_priority || "medium",
                            suggested_eisenhower_quadrant:
                                aiSuggestions.suggested_eisenhower_quadrant ||
                                null,
                        };
                        this.showAiSuggestions = true;
                    } catch (parseError) {
                        console.error(
                            "Gagal memparsing respons JSON dari AI:",
                            parseError
                        );
                        Toast.fire({
                            icon: "error",
                            title:
                                "Respons AI tidak dalam format JSON yang diharapkan. Raw response: " +
                                data.ai_parsed_response,
                        });
                        this.showAiSuggestions = false;
                    }
                } else {
                    Toast.fire({
                        icon: "error",
                        title: "Analisis AI gagal: " + data.message,
                    });
                    this.showAiSuggestions = false;
                }
            } catch (error) {
                console.error("Error saat memanggil AI:", error);
                Toast.fire({
                    icon: "error",
                    title:
                        "Terjadi kesalahan saat menghubungi AI: " +
                        error.message,
                });
                this.showAiSuggestions = false;
            } finally {
                this.isAnalyzingAi = false;
                this.updateUiState();
            }
        },

        async applyAiSuggestions() {
            const finalMainTask =
                this.aiSuggestionsData.main_task || this.taskForm.task.trim();

            if (!finalMainTask) {
                Toast.fire({
                    icon: "info",
                    title: "Tidak ada tugas utama yang disarankan AI atau di input untuk diterapkan.",
                });
                return;
            }

            this.isLoading = true;

            let projectId = null;

            // Step 1: Create Project if suggested
            if (this.aiSuggestionsData.suggested_project_name) {
                try {
                    // PERBAIKAN URL: Menggunakan '/projects' karena rute Anda tidak memiliki prefix '/api'
                    const projectResponse = await fetch("projects", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify({
                            name: this.aiSuggestionsData.suggested_project_name,
                            description: `Proyek yang disarankan oleh AI untuk tugas: "${finalMainTask}"`,
                        }),
                    });

                    if (!projectResponse.ok) {
                        const errorData = await projectResponse.json();
                        throw new Error(
                            errorData.message || "Gagal membuat proyek baru."
                        );
                    }

                    const projectData = await projectResponse.json();
                    projectId = projectData.project.id;
                    Toast.fire({
                        icon: "success",
                        title: `Proyek "${projectData.project.name}" berhasil dibuat!`,
                    });

                    this.projects.push(projectData.project);
                    // Anda mungkin perlu memuat ulang daftar proyek di sidebar jika tidak otomatis terupdate
                    // this.fetchProjects(); // Jika Anda memiliki metode ini
                } catch (error) {
                    console.error("Error creating project:", error);
                    Toast.fire({
                        icon: "error",
                        title:
                            "Gagal membuat proyek dari saran AI: " +
                            error.message,
                    });
                    this.isLoading = false;
                    return;
                }
            }

            // Step 2: Create Main Task
            try {
                const taskPayload = {
                    task: finalMainTask,
                    project_id: projectId,
                    notes: this.aiSuggestionsData.task_analysis || "",
                    priority: this.aiSuggestionsData.suggested_priority,
                    eisenhower_quadrant:
                        this.aiSuggestionsData.suggested_eisenhower_quadrant,
                    subtasks: JSON.stringify(
                        this.aiSuggestionsData.suggested_subtasks.map((s) => ({
                            task: s,
                            status: "todo",
                        }))
                    ),
                };

                // PERBAIKAN URL: Menggunakan '/todos' karena rute Anda tidak memiliki prefix '/api'
                const taskResponse = await fetch("todos", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify(taskPayload),
                });

                if (!taskResponse.ok) {
                    const errorData = await taskResponse.json();
                    throw new Error(
                        errorData.message || "Gagal membuat tugas baru."
                    );
                }

                const taskData = await taskResponse.json();
                Toast.fire({
                    icon: "success",
                    title: `Tugas "${taskData.task.task}" berhasil dibuat!`,
                });

                if (!this.tasks[taskData.task.status]) {
                    this.tasks[taskData.task.status] = [];
                }
                this.tasks[taskData.task.status].push(taskData.task);

                this.showAiSuggestions = false;
                this.aiSuggestionsData = {
                    suggested_project_name: null,
                    main_task: "",
                    suggested_subtasks: [],
                    suggested_priority: "medium",
                    suggested_eisenhower_quadrant: null,
                };
                this.taskForm.task = "";
                this.updateUiState();
            } catch (error) {
                console.error("Error creating task:", error);
                Toast.fire({
                    icon: "error",
                    title:
                        "Gagal membuat tugas dari saran AI: " + error.message,
                });
            } finally {
                this.isLoading = false;
            }
        },

        listenForStorageChanges() {
            window.addEventListener("storage", (event) => {
                // event.key adalah nama item di localStorage, cth: 'pomodoro_is_running'

                // event.newValue adalah nilai barunya

                console.log(`State berubah di tab lain: ${event.key}`); // Jika timer dihentikan atau dijalankan di tab lain, // maka tab ini harus menyesuaikan diri.

                if (event.key === "pomodoro_is_running") {
                    const isRunningInAnotherTab = JSON.parse(event.newValue); // Sinkronkan state lokal

                    this.isRunning = isRunningInAnotherTab; // Jika timer dihentikan dari tab lain, hentikan interval di sini juga

                    if (!isRunningInAnotherTab && this.timerInterval) {
                        clearInterval(this.timerInterval);
                    }
                } // Anda juga bisa sinkronkan state lain seperti cycle, mode, dll.

                if (event.key === "pomodoro_cycle") {
                    this.pomodoroCycle = JSON.parse(event.newValue);
                }
            });
        },

        get matrixTasks() {
            // [PERBAIKAN] Ambil semua tugas KECUALI yang sudah 'done'
            const allTasks = Object.entries(this.tasks)
                .filter(([status, taskList]) => status !== "done")
                .map(([status, taskList]) => taskList)
                .flat();

            const grouped = allTasks.reduce((acc, task) => {
                const quadrant = task.eisenhower_quadrant || "unsorted";
                if (!acc[quadrant]) acc[quadrant] = [];
                acc[quadrant].push(task);
                return acc;
            }, {});

            return {
                do: grouped.do || [],
                schedule: grouped.schedule || [],
                delegate: grouped.delegate || [],
                delete: grouped.delete || [],
                unsorted: grouped.unsorted || [],
            };
        },

        get kanbanTasks() {
            const kanbanData = {};

            for (const status in this.tasks) {
                if (Object.hasOwnProperty.call(this.tasks, status)) {
                    // Filter tugas untuk setiap kolom status

                    kanbanData[status] = this.tasks[status].filter(
                        (task) =>
                            task.eisenhower_quadrant !== "delegate" &&
                            task.eisenhower_quadrant !== "delete"
                    );
                }
            }

            return kanbanData;
        },

        get timerProgress() {
            // Tentukan total durasi sesi saat ini dalam detik
            const totalDurationInSeconds =
                (this.pomodoroMode === "work"
                    ? this.lastWorkDuration
                    : this.pomodoroDurations[this.pomodoroMode]) * 60;

            // Hindari pembagian dengan nol jika durasi tidak valid
            if (totalDurationInSeconds <= 0) return 0;

            // Hitung waktu yang telah berlalu
            const elapsedSeconds = totalDurationInSeconds - this.timeLeft;

            // Hitung persentase progres
            const progress = (elapsedSeconds / totalDurationInSeconds) * 100;

            // Pastikan nilai tidak melebihi 100
            return Math.min(100, progress);
        },

        get timerDisplay() {
            const minutes = Math.floor(this.timeLeft / 60);

            const seconds = this.timeLeft % 60;

            return `${String(minutes).padStart(2, "0")}:${String(
                seconds
            ).padStart(2, "0")}`;
        },

        helpers: {
            formatDateTime(dateString) {
                if (!dateString) return null;

                const date = new Date(dateString);

                const dateOptions = {
                    day: "2-digit",

                    month: "short",

                    year: "numeric",
                };

                const timeOptions = {
                    hour: "2-digit",

                    minute: "2-digit",

                    hour12: false,
                };

                const formattedDate = date.toLocaleDateString(
                    "id-ID",

                    dateOptions
                );

                const formattedTime = date

                    .toLocaleTimeString("id-ID", timeOptions)

                    .replace(".", ":");

                return `${formattedDate}, ${formattedTime}`;
            },

            isOverdue(task) {
                if (!task.due_date || task.status === "done") return false;

                return new Date(task.due_date) < new Date();
            },
        },

        async filterTasksByProject(projectId, baseUrl) {
            this.isLoading = true;
            let url = baseUrl;

            if (projectId) {
                url += `?project_id=${projectId}`;
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!response.ok) {
                    throw new Error(
                        `Gagal mengambil data. Status: ${response.status}`
                    );
                }

                const newTasks = await response.json();

                this.tasks = {
                    todo: newTasks.todo || [],
                    in_progress: newTasks.in_progress || [],
                    done: newTasks.done || [],
                };

                if (this.pomodoroTaskId) {
                    const allNewTasks = Object.values(this.tasks).flat();
                    const activeTaskStillExists = allNewTasks.some(
                        (task) => task.id === this.pomodoroTaskId
                    );

                    if (!activeTaskStillExists) {
                        console.log(
                            "Tugas Pomodoro aktif tidak ada di filter ini. Mereset timer."
                        );
                        this.resetPomodoroState();
                    }
                }
            } catch (error) {
                console.error("Gagal memfilter tugas:", error);

                // [PERUBAHAN] Menggunakan SweetAlert untuk notifikasi error
                Swal.fire({
                    icon: "error",
                    title: "Gagal Memuat Tugas",
                    text: "Terjadi masalah saat memfilter tugas. Silakan coba lagi.",
                    confirmButtonColor: "#007BFF",
                });
            } finally {
                this.isLoading = false;
            }
        },

        showPriorityInfo() {
            Swal.fire({
                title: "<strong>Informasi Prioritas Tugas</strong>",
                icon: "info",
                html: `
            <div class="text-left space-y-3 p-4">
                <p>Prioritas membantu Anda mengurutkan tugas mana yang harus dikerjakan lebih dulu.</p>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Fokus Hari Ini:</strong> Tugas paling penting yang harus selesai hari ini.</li>
                    <li><strong>Tinggi:</strong> Tugas penting dengan tenggat waktu dekat.</li>
                    <li><strong>Sedang:</strong> Tugas rutin sehari-hari.</li>
                    <li><strong>Rendah:</strong> Tugas yang bisa ditunda jika perlu.</li>
                </ul>
            </div>
        `,
                confirmButtonText: "Mengerti",
                confirmButtonColor: "#007BFF",
            });
        },

        showEisenhowerInfo() {
            Swal.fire({
                title: "<strong>Informasi Kuadran Eisenhower</strong>",
                icon: "info",
                html: `
            <div class="text-left space-y-3 p-4">
                <p>Matriks Eisenhower membantu Anda mengkategorikan tugas berdasarkan urgensi dan kepentingannya.</p>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Do (Penting & Mendesak):</strong> Kerjakan segera! Ini adalah prioritas utama Anda.</li>
                    <li><strong>Schedule (Penting & Tidak Mendesak):</strong> Jadwalkan waktu untuk mengerjakannya. Jangan diabaikan.</li>
                    <li><strong>Delegate (Tidak Penting & Mendesak):</strong> Serahkan kepada orang lain jika memungkinkan.</li>
                    <li><strong>Delete (Tidak Penting & Tidak Mendesak):</strong> Abaikan atau hapus. Ini adalah distraksi.</li>
                </ul>
            </div>
        `,
                confirmButtonText: "Mengerti",
                confirmButtonColor: "#007BFF",
            });
        },

        // --- Fungsi Tugas (Tasks) ---

        openCreateTaskModal() {
            this.editingTask = null;
            this.taskForm = {
                task: "",
                project_id: "",
                notes: "",
                due_date: "",
                priority: "medium",
                eisenhower_quadrant: "",
                file: null,
                subtasks: [],
            };
            this.isEditMode = true;
            this.isTaskModalOpen = true;
        },

        openEditTaskModal(task) {
            this.editingTask = task;
            this.taskForm = {
                task: task.task,
                project_id: task.project_id || "",
                notes: task.notes || "",
                due_date: task.due_date
                    ? task.due_date.slice(0, 16).replace(" ", "T")
                    : "",
                priority: task.priority || "medium",
                eisenhower_quadrant: task.eisenhower_quadrant || "",
                file: null,
                subtasks: task.subtasks ? [...task.subtasks] : [],
                pomodoro_custom_duration: task.pomodoro_custom_duration || null,
            };
            this.isEditMode = false;
            this.isTaskModalOpen = true;
        },

        addNewSubtask() {
            this.taskForm.subtasks.push({ id: null, task: "", status: "todo" });

            // [PERUBAHAN] Tampilkan notifikasi success
            Toast.fire({
                icon: "success",
                title: "Sub-tugas berhasil ditambahkan",
            });
        },

        removeSubtask(index) {
            this.taskForm.subtasks.splice(index, 1);

            // [PERUBAHAN] Tampilkan notifikasi success
            Toast.fire({
                icon: "success",
                title: "Sub-tugas berhasil dihapus",
            });
        },

        switchToEditMode() {
            this.isEditMode = true;
        },

        closeTaskModal() {
            this.isTaskModalOpen = false;

            setTimeout(() => {
                this.isEditMode = false;
            }, 300);
        },

        async toggleSubtaskStatus(subtask, isChecked) {
            const originalStatus = subtask.status;
            const newStatus = isChecked ? "done" : "todo";
            subtask.status = newStatus;

            try {
                const response = await fetch(
                    `/user/todos/${subtask.id}/status`,
                    {
                        method: "PATCH",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            Accept: "application/json",
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({ status: newStatus }),
                    }
                );

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(
                        data.message || "Gagal memperbarui sub-tugas."
                    );
                }

                if (data.parentTask) {
                    this.updateLocalTasks(data.parentTask);
                }

                // [PERUBAHAN] Tampilkan notifikasi sukses (toast)
                Toast.fire({
                    icon: "success",
                    title: `Sub-tugas ditandai '${
                        newStatus === "done" ? "Selesai" : "To Do"
                    }'`,
                });
            } catch (error) {
                subtask.status = originalStatus; // Rollback

                // [PERUBAHAN] Tampilkan notifikasi error (modal)
                Swal.fire({
                    icon: "error",
                    title: "Update Gagal",
                    text: error.message,
                    confirmButtonColor: "#007BFF",
                });
            }
        },

        async submitTaskForm(event) {
            const formData = new FormData(event.target);
            const url = this.editingTask
                ? `/user/todos/${this.editingTask.id}`
                : "/user/todos";
            if (this.editingTask) formData.append("_method", "PUT");
            formData.append("subtasks", JSON.stringify(this.taskForm.subtasks));

            try {
                const response = await fetch(url, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        Accept: "application/json",
                    },
                });

                const data = await response.json();
                if (!response.ok || !data.task) {
                    // [PERUBAHAN] Menangkap error validasi dari server
                    const errorMessage = data.errors
                        ? Object.values(data.errors).join("\n")
                        : data.message || "Gagal menyimpan tugas.";
                    throw new Error(errorMessage);
                }

                this.updateLocalTasks(data.task);
                if (data.task.project_id && !this.editingTask) {
                    const project = this.projects.find(
                        (p) => p.id === data.task.project_id
                    );
                    if (project) project.todos_count++;
                }

                this.closeTaskModal();

                // [PERUBAHAN] Notifikasi sukses dengan Toast
                Toast.fire({
                    icon: "success",
                    title: data.message,
                });
            } catch (error) {
                // [PERUBAHAN] Notifikasi error dengan modal
                Swal.fire({
                    icon: "error",
                    title: "Gagal Menyimpan",
                    text: error.message,
                    confirmButtonColor: "#007BFF",
                });
            }
        },

        async deleteTask(todoId) {
            // [PERUBAHAN] Mengganti confirm() dengan Swal.fire()
            Swal.fire({
                title: "Anda yakin?",
                text: "Tugas ini akan dihapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Lanjutkan logika penghapusan jika dikonfirmasi
                    const originalTask = this.findLocalTask(todoId); // Simpan untuk rollback

                    if (this.pomodoroTaskId === todoId) {
                        this.resetPomodoroState();
                    }
                    this.removeLocalTask(todoId);

                    // Tampilkan notifikasi toast bahwa tugas dihapus
                    Toast.fire({
                        icon: "success",
                        title: "Tugas berhasil dihapus",
                    });

                    try {
                        await fetch(`/user/todos/${todoId}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                                Accept: "application/json",
                            },
                        });
                    } catch (error) {
                        // Jika gagal, kembalikan tugas yang dihapus (rollback)
                        if (originalTask) this.updateLocalTasks(originalTask);
                        Swal.fire(
                            "Gagal!",
                            "Gagal menghapus tugas dari server. Silakan muat ulang.",
                            "error"
                        );
                    }
                }
            });
        },

        async updateTaskStatus(todoId, newStatus) {
            // [IMPROVISASI] Cek apakah tugas yang diselesaikan adalah tugas pomodoro yang aktif
            if (
                newStatus === "done" &&
                this.isPomodoroActive &&
                this.pomodoroTaskId === todoId
            ) {
                // Jika ya, hentikan dan reset timer terlebih dahulu
                this.stopAndResetTimer();
            }

            // --- Logika asli Anda dimulai dari sini (tidak ada yang diubah) ---
            const originalTask = this.findLocalTask(todoId);
            if (!originalTask) return;
            const originalStatus = originalTask.status;
            this.removeLocalTask(todoId);
            originalTask.status = newStatus;
            this.updateLocalTasks(originalTask);

            try {
                const response = await fetch(`/user/todos/${todoId}/status`, {
                    method: "PATCH",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ status: newStatus }),
                });

                const data = await response.json();
                if (!response.ok || !data.task)
                    throw new Error(data.message || "Gagal memperbarui status");

                this.updateLocalTasks(data.task);
            } catch (error) {
                console.error("Update task status error:", error);

                Swal.fire({
                    icon: "error",
                    title: "Gagal Sinkronisasi",
                    text: error.message,
                    confirmButtonColor: "#007BFF",
                });

                // Rollback jika gagal
                const taskToRollback = this.findLocalTask(todoId);
                if (taskToRollback) {
                    this.removeLocalTask(todoId);
                    taskToRollback.status = originalStatus;
                    this.updateLocalTasks(taskToRollback);
                }
            }
        },

        async togglePin(todoId) {
            try {
                const response = await fetch(`/user/todos/${todoId}/pin`, {
                    method: "PATCH",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        Accept: "application/json",
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    const task = this.findLocalTask(todoId);
                    if (task) {
                        task.priority = data.priority;

                        // [PERUBAHAN] Notifikasi sukses dengan Toast
                        Toast.fire({
                            icon: "success",
                            title: `Tugas ${
                                data.priority === "focus"
                                    ? "disematkan"
                                    : "dilepas"
                            }`,
                        });
                    }
                } else {
                    throw new Error(
                        data.message || "Gagal mengubah prioritas."
                    );
                }
            } catch (error) {
                // [PERUBAHAN] Notifikasi error dengan modal
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: error.message,
                    confirmButtonColor: "#007BFF",
                });
                console.error("Toggle pin error:", error);
            }
        },

        // --- Fungsi Proyek ---

        openCreateProjectModal() {
            this.editingProjectId = null;
            this.projectForm = { name: "", description: "" };
            this.isProjectModalOpen = true;
        },

        openEditProjectModal(project) {
            this.editingProjectId = project.id;
            this.projectForm = {
                name: project.name,
                description: project.description,
            };
            this.isProjectModalOpen = true;
        },

        closeProjectModal() {
            this.isProjectModalOpen = false;
        },

        updateLocalProjects(updatedProject) {
            // Cari index proyek yang sudah ada
            const index = this.projects.findIndex(
                (p) => p.id === updatedProject.id
            );

            if (index > -1) {
                // Jika ditemukan (mode update), ganti data lama dengan yang baru
                this.projects[index] = updatedProject;
            } else {
                // Jika tidak ditemukan (mode create), tambahkan ke awal daftar
                this.projects.unshift(updatedProject);
            }
        },

        async submitProjectForm(event) {
            const formData = new FormData(event.target);
            const url = this.editingProjectId
                ? `/user/projects/${this.editingProjectId}`
                : "/user/projects";
            if (this.editingProjectId) formData.append("_method", "PUT");

            try {
                const response = await fetch(url, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                    },
                });

                const data = await response.json();
                if (!response.ok) {
                    const errorMessage = data.errors
                        ? Object.values(data.errors).join("\n")
                        : data.message;
                    throw new Error(errorMessage);
                }

                this.updateLocalProjects(data.project);
                this.closeProjectModal();

                // [PERUBAHAN] Notifikasi sukses dengan Toast
                Toast.fire({
                    icon: "success",
                    title: data.message,
                });
            } catch (error) {
                // [PERUBAHAN] Notifikasi error dengan modal
                Swal.fire({
                    icon: "error",
                    title: "Gagal Menyimpan Proyek",
                    text: error.message,
                    confirmButtonColor: "#007BFF",
                });
            }
        },

        async deleteProject(projectId) {
            // [PERUBAHAN] Mengganti confirm() dengan Swal.fire()
            Swal.fire({
                title: "Anda yakin?",
                text: "Proyek dan semua tugas di dalamnya akan dihapus!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Simpan data asli untuk rollback jika gagal
                    const projectIndex = this.projects.findIndex(
                        (p) => p.id === projectId
                    );
                    if (projectIndex === -1) return;
                    const originalProject = this.projects[projectIndex];

                    if (this.pomodoroTaskId) {
                        const activeTask = this.findLocalTask(
                            this.pomodoroTaskId
                        );
                        if (activeTask && activeTask.project_id === projectId) {
                            this.resetPomodoroState();
                        }
                    }

                    // Optimistic update
                    this.projects.splice(projectIndex, 1);

                    Toast.fire({
                        icon: "success",
                        title: "Proyek berhasil dihapus",
                    });

                    try {
                        const response = await fetch(
                            `/user/projects/${projectId}`,
                            {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .getAttribute("content"),
                                    Accept: "application/json",
                                },
                            }
                        );
                        if (!response.ok)
                            throw new Error("Gagal menghapus di server.");
                    } catch (error) {
                        // Rollback jika gagal
                        this.projects.splice(projectIndex, 0, originalProject);
                        Swal.fire(
                            "Gagal!",
                            "Gagal menghapus proyek dari server.",
                            "error"
                        );
                    }
                }
            });
        },

        async updateTaskQuadrant(taskId, newQuadrant) {
            const originalTask = this.findLocalTask(taskId);
            if (!originalTask) return;
            const originalQuadrant = originalTask.eisenhower_quadrant;

            originalTask.eisenhower_quadrant = newQuadrant;
            this.updateLocalTasks(originalTask);

            try {
                const response = await fetch(`/user/todos/${taskId}/quadrant`, {
                    method: "PATCH",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ quadrant: newQuadrant }),
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(
                        data.message || "Gagal memperbarui kuadran."
                    );
                }

                this.updateLocalTasks(data.task);

                // [PERUBAHAN] Notifikasi sukses dengan Toast
                Toast.fire({
                    icon: "info",
                    title: "Kuadran tugas diperbarui",
                });
            } catch (error) {
                // [PERUBAHAN] Notifikasi error dengan modal
                Swal.fire({
                    icon: "error",
                    title: "Gagal Update",
                    text: error.message,
                    confirmButtonColor: "#007BFF",
                });

                // Rollback
                const taskToRollback = this.findLocalTask(taskId);
                if (taskToRollback) {
                    taskToRollback.eisenhower_quadrant = originalQuadrant;
                    this.updateLocalTasks(taskToRollback);
                }
            }
        },

        // ===================================================================
        // === POMODORO CORE LOGIC
        // ===================================================================
        startFocusOn(task) {
            // Penjaga 1: Cek apakah sudah ada sesi lain yang aktif.
            if (this.isPomodoroActive) {
                // [PERUBAHAN] Ganti alert dengan Swal.fire
                Swal.fire({
                    icon: "warning",
                    title: "Sesi Lain Sedang Aktif",
                    text: `Selesaikan dulu sesi untuk "${this.pomodoroTaskName}" sebelum memulai yang baru.`,
                    confirmButtonColor: "#007BFF",
                });
                return;
            }

            // Penjaga 2: Cek sub-tugas sebelum memulai.
            const hasSubtasks = task.subtasks && task.subtasks.length > 0;
            if (hasSubtasks) {
                const allSubtasksDone = task.subtasks.every(
                    (st) => st.status === "done"
                );
                if (!allSubtasksDone) {
                    // [PERUBAHAN] Ganti alert dengan Swal.fire
                    Swal.fire({
                        icon: "warning",
                        title: "Sub-tugas Belum Selesai",
                        text: "Harap selesaikan semua sub-tugas terlebih dahulu sebelum memulai sesi fokus untuk tugas utama ini.",
                        confirmButtonColor: "#007BFF",
                    });
                    return; // Hentikan proses jika sub-tugas belum selesai.
                }
            }

            // Jika semua pengecekan lolos, barulah lanjutkan.
            this.setupNewSessionFor(task);
            this.isPomodoroActive = true;
            this.startTimer();
        },

        prepareNextSession() {
            this.isRunning = false;
            clearInterval(this.timerInterval);
            this.activePomodoroSessionId = null; // Siap untuk sesi baru di database
            this.pomodoroEndTime = null;
        },

        setupNewSessionFor(task) {
            this.resetPomodoroState();
            this.pomodoroTaskName = task.task;
            this.pomodoroTaskId = task.id;
            this.pomodoroMode = "work";
            this.pomodoroCycle = task.pomodoro_cycles_completed || 0;

            const lastSession =
                task.pomodoro_sessions && task.pomodoro_sessions.length > 0
                    ? task.pomodoro_sessions[0]
                    : null;

            if (
                lastSession &&
                lastSession.status === "interrupted" &&
                lastSession.remaining_seconds
            ) {
                this.timeLeft = lastSession.remaining_seconds;
                this.activePomodoroSessionId = lastSession.id;
                this.lastWorkDuration = lastSession.duration_minutes;
            } else {
                const workDuration =
                    task.pomodoro_custom_duration ||
                    this.pomodoroDurations.work;
                this.lastWorkDuration = workDuration;
                this.timeLeft = workDuration * 60;
            }
        },

        toggleTimer() {
            if (this.timeLeft <= 0) {
                this.handleSessionFinish();
                return;
            }
            if (this.isRunning) {
                this.pauseTimer();
            } else {
                this.startTimer();
            }
        },

        async startTimer(duration = this.lastWorkDuration) {
            if (this.isRunning) return;
            this.isRunning = true;

            // Logika untuk melanjutkan sesi
            if (this.activePomodoroSessionId) {
                fetch(
                    `/user/pomodoro-sessions/${this.activePomodoroSessionId}/resume`,
                    {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            Accept: "application/json",
                        },
                    }
                ).catch((e) => {
                    console.error("Gagal melanjutkan sesi di server", e);
                    // [PERUBAHAN] Notifikasi error jika gagal resume
                    Swal.fire({
                        icon: "error",
                        title: "Gagal Sinkronisasi",
                        text: "Gagal melanjutkan sesi di server. Timer tetap berjalan, namun progres tidak akan tersimpan jika halaman di-refresh.",
                        confirmButtonColor: "#007BFF",
                    });
                });

                this.resumeTimer();
                return;
            }

            // Logika untuk memulai sesi BARU
            const now = new Date();
            try {
                const response = await fetch("/user/pomodoro-sessions", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({
                        todo_id: this.pomodoroTaskId,
                        duration_minutes: duration,
                        start_time: now.toISOString(),
                        type: this.pomodoroMode,
                    }),
                });
                const data = await response.json();
                if (data.session) {
                    this.activePomodoroSessionId = data.session.id;
                } else {
                    throw new Error("Server tidak mengembalikan data sesi.");
                }
            } catch (e) {
                console.error("Gagal membuat sesi pomodoro baru", e);
                this.isRunning = false; // Hentikan timer jika gagal membuat sesi

                // [PERUBAHAN] Notifikasi error jika gagal membuat sesi baru
                Swal.fire({
                    icon: "error",
                    title: "Gagal Memulai Sesi",
                    text: "Tidak dapat membuat sesi Pomodoro baru di server. Silakan periksa koneksi Anda dan coba lagi.",
                    confirmButtonColor: "#007BFF",
                });
                return;
            }
            this.resumeTimer();
        },

        resumeTimer() {
            clearInterval(this.timerInterval);
            this.isRunning = true;
            this.pomodoroEndTime = new Date(
                new Date().getTime() + this.timeLeft * 1000
            ).toISOString();
            this.timerInterval = setInterval(() => {
                if (this.timeLeft <= 0) {
                    // <-- PERBAIKAN
                    this.handleSessionFinish();
                } else {
                    this.timeLeft--;
                }
            }, 1000);
        },

        async pauseTimer() {
            if (!this.isRunning || !this.activePomodoroSessionId) return;

            this.isRunning = false;
            clearInterval(this.timerInterval);

            // [PERUBAHAN] Tampilkan notifikasi toast bahwa timer dijeda
            Toast.fire({
                icon: "info",
                title: "Timer dijeda",
            });

            try {
                await fetch(
                    `/user/pomodoro-sessions/${this.activePomodoroSessionId}/pause`,
                    {
                        method: "PATCH",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({
                            remaining_seconds: this.timeLeft,
                        }),
                    }
                );
            } catch (e) {
                console.error("Gagal menyimpan status jeda pomodoro", e);
                // [PERUBAHAN] Tampilkan notifikasi error jika gagal menyimpan
                Swal.fire({
                    icon: "error",
                    title: "Gagal Menyimpan Progres",
                    text: "Perubahan Anda mungkin tidak tersimpan jika halaman di-refresh.",
                    confirmButtonColor: "#007BFF",
                });
            }
        },

        stopAndResetTimer() {
            // [PERUBAHAN] Ganti confirm() dengan Swal.fire()
            Swal.fire({
                title: "Hentikan Sesi?",
                text: "Anda yakin ingin menghentikan sesi ini dan mereset timer?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hentikan!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Logika dijalankan hanya jika pengguna mengonfirmasi
                    clearInterval(this.timerInterval);
                    if (this.activePomodoroSessionId) {
                        this.updatePomodoroStatus("interrupted");
                    }

                    this.isPomodoroActive = false;
                    this.resetPomodoroState();

                    // Beri notifikasi toast bahwa sesi dihentikan
                    Toast.fire({
                        icon: "info",
                        title: "Sesi dihentikan.",
                    });
                }
            });
        },

        resetPomodoroState() {
            this.isRunning = false;
            this.isPomodoroActive = false;
            this.pomodoroEndTime = null;
            this.pomodoroTaskId = null;
            this.pomodoroTaskName = "Tugas Umum";
            this.activePomodoroSessionId = null;
            // this.pomodoroCycle = 0; // Sebaiknya jangan direset di sini
            this.pomodoroMode = "work";
            this.timeLeft = this.pomodoroDurations.work * 60;
            clearInterval(this.timerInterval);
        },

        // ===================================================================
        // === POMODORO LIFECYCLE & FLOW
        // ===================================================================

        async handleSessionFinish() {
            const sound = document.getElementById("pomodoro-finish-sound");
            if (sound) sound.play();
            clearInterval(this.timerInterval);
            this.timeLeft = 0;
            this.isRunning = false;

            // Simpan ID tugas saat ini sebelum di-reset
            const finishedTaskId = this.pomodoroTaskId;

            await this.updatePomodoroStatus("completed");

            if (this.pomodoroMode === "work") {
                // [PERUBAHAN] Notifikasi setelah sesi kerja selesai
                Swal.fire({
                    icon: "success",
                    title: "Sesi Fokus Selesai!",
                    text: "Waktunya istirahat sejenak.",
                    confirmButtonText: "Mulai Istirahat",
                    confirmButtonColor: "#007BFF",
                }).then(() => {
                    // Lanjutkan ke sesi istirahat setelah notifikasi ditutup
                    this.handleWorkSessionFinish();
                });
            } else {
                // [PERUBAHAN] Notifikasi setelah sesi istirahat (dan tugas) selesai
                Swal.fire({
                    icon: "success",
                    title: "Kerja Bagus!",
                    text: "Tugas telah ditandai selesai.",
                    confirmButtonColor: "#007BFF",
                }).then(async () => {
                    // Selesaikan tugas dan reset timer setelah notifikasi ditutup
                    if (finishedTaskId) {
                        await this.updateTaskStatus(finishedTaskId, "done");
                    }
                    this.resetPomodoroState();
                });
            }
        },

        handleWorkSessionFinish() {
            this.pomodoroCycle++;
            const task = this.findLocalTask(this.pomodoroTaskId);
            if (task) task.pomodoro_cycles_completed = this.pomodoroCycle;

            if (this.pomodoroCycle > 0 && this.pomodoroCycle % 4 === 0) {
                this.switchToLongBreak();
            } else {
                this.switchToShortBreak();
            }
        },

        switchToWork() {
            this.prepareNextSession(); // <-- Perbaikan
            this.pomodoroMode = "work";
            this.timeLeft = this.lastWorkDuration * 60;
        },

        switchToShortBreak() {
            this.prepareNextSession();
            this.pomodoroMode = "short_break";

            // [PERBAIKAN] Kalkulasi berbasis detik
            const workSeconds = this.lastWorkDuration * 60;
            const breakSeconds = Math.round(workSeconds / 5); // 1/5 dari durasi kerja

            // Waktu istirahat minimal 60 detik (1 menit)
            this.timeLeft = Math.max(60, breakSeconds);

            // Panggil startTimer dengan durasi baru untuk disimpan ke DB
            const breakDurationForDB = Math.round(this.timeLeft / 60);
            this.startTimer(breakDurationForDB);
        },

        switchToLongBreak() {
            this.prepareNextSession();
            this.pomodoroMode = "long_break";
            const breakDuration = this.pomodoroDurations.long_break;
            this.timeLeft = breakDuration * 60;
            this.pomodoroCycle = 0;
            const task = this.findLocalTask(this.pomodoroTaskId);
            if (task) task.pomodoro_cycles_completed = 0;

            // Kirim durasi ke startTimer
            this.startTimer(breakDuration);
        },

        async updatePomodoroStatus(newStatus) {
            if (!this.activePomodoroSessionId) return;
            try {
                await fetch(
                    `/user/pomodoro-sessions/${this.activePomodoroSessionId}/status`,
                    {
                        method: "PATCH",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({
                            status: newStatus,
                            end_time: new Date().toISOString(),
                        }),
                    }
                );
            } catch (e) {
                console.error("Gagal update status pomodoro", e);
            }
        },

        // ===================================================================
        // === COMPUTED PROPERTIES & HELPERS
        // ===================================================================
        get timerDisplay() {
            const minutes = Math.floor(this.timeLeft / 60);
            const seconds = this.timeLeft % 60;
            return `${String(minutes).padStart(2, "0")}:${String(
                seconds
            ).padStart(2, "0")}`;
        },

        // --- Internal Helper Functions
        findLocalTask(taskId) {
            if (!taskId) return null;
            for (const status in this.tasks) {
                const task = this.tasks[status].find((t) => t.id == taskId);
                if (task) return task;
            }
            return null;
        },

        removeLocalTask(taskId) {
            for (const status in this.tasks) {
                const index = this.tasks[status].findIndex(
                    (t) => t.id == taskId
                );
                if (index > -1) {
                    this.tasks[status].splice(index, 1);
                    return;
                }
            }
        },

        updateLocalTasks(updatedTask) {
            this.removeLocalTask(updatedTask.id);
            if (!this.tasks[updatedTask.status]) {
                this.tasks[updatedTask.status] = [];
            }
            this.tasks[updatedTask.status].unshift(updatedTask);
        },

        restorePausedSessionFromData(taskGroups) {
            let restored = false;
            for (const status in taskGroups) {
                for (const task of taskGroups[status]) {
                    if (
                        task.pomodoro_sessions &&
                        task.pomodoro_sessions.length > 0
                    ) {
                        const pausedSession = task.pomodoro_sessions[0];
                        this.pomodoroTaskId = pausedSession.todo_id;
                        this.pomodoroTaskName = task.task;
                        this.activePomodoroSessionId = pausedSession.id;
                        this.timeLeft = pausedSession.remaining_seconds;
                        this.isRunning = false;
                        this.pomodoroMode = pausedSession.type;
                        restored = true;
                        break;
                    }
                }
                if (restored) break;
            }
            if (!restored) {
                this.resetPomodoroState();
            }
        },

        listenForStorageChanges() {
            window.addEventListener("storage", (event) => {
                if (event.key === "pomodoro_is_running") {
                    this.isRunning = JSON.parse(event.newValue);
                    if (!this.isRunning) clearInterval(this.timerInterval);
                }
                if (event.key === "pomodoro_cycle") {
                    this.pomodoroCycle = JSON.parse(event.newValue);
                }
            });
        },

        async handleDrop(event, newStatus) {
            const taskId = event.dataTransfer.getData("text/plain");

            const task = this.findLocalTask(taskId); // Jangan lakukan apa-apa jika drop di kolom yang sama

            if (task && task.status !== newStatus) {
                await this.updateTaskStatus(taskId, newStatus);
            }
        },

        async handleMatrixDrop(event, newQuadrant) {
            const taskId = event.dataTransfer.getData("text/plain");

            const task = this.findLocalTask(taskId); // Jangan lakukan apa-apa jika drop di kuadran yang sama

            if (task && task.eisenhower_quadrant !== newQuadrant) {
                await this.updateTaskQuadrant(taskId, newQuadrant);
            }
        },
    };
}
