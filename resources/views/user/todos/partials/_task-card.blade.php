@props(['task', 'interruptedSession' => null])

<div draggable="true" data-task-id="{{ $task->id }}" data-quadrant="{{ $task->eisenhower_quadrant }}" 
     class="task-card group relative bg-white p-3 rounded-lg shadow-sm border border-gray-200 cursor-grab hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
    
    @if(isset($task->priority) && $task->priority)
        <div @class([
            'absolute left-0 top-0 h-full w-1.5 rounded-l-lg',
            'bg-red-500' => $task->priority == 'high',
            'bg-yellow-500' => $task->priority == 'medium',
            'bg-gray-400' => $task->priority == 'low',
        ])></div>
    @endif

    <div class="pl-2">
        <div class="flex justify-between items-start">
            <p class="font-semibold text-gray-800 flex-1 pr-2">{{ $task->title }}</p>
            
            <div class="hidden group-hover:flex items-center space-x-1 flex-shrink-0">
                @if($interruptedSession && $interruptedSession->todo_id == $task->id)
                    <button class="resume-pomodoro-button p-2 text-gray-400 hover:text-blue-600" title="Lanjutkan Sesi Pomodoro" data-session-id="{{ $interruptedSession->id }}">
                        <i class="fas fa-play-circle fa-sm"></i>
                    </button>
                @else
                    <button class="start-pomodoro-button p-2 text-gray-400 hover:text-green-600" title="Mulai Sesi Pomodoro">
                        <i class="fas fa-play-circle fa-sm"></i>
                    </button>
                @endif
                
                <button class="edit-task-trigger p-2 text-gray-400 hover:text-yellow-500" title="Ubah Tugas">
                    <i class="fas fa-pencil-alt fa-sm"></i>
                </button>
                
                <button class="delete-task-button p-2 text-gray-400 hover:text-red-600" title="Hapus Tugas">
                    <i class="fas fa-trash-can fa-sm"></i>
                </button>
            </div>
        </div>

        @if($task->description)
            <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                {{ $task->description }}
            </p>
        @endif

        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
            @if($task->project)
                <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-800 font-medium px-2 py-0.5 rounded-full">
                    <i class="fas fa-folder"></i>
                    {{ $task->project->name }}
                </span>
            @endif

            @if($task->due_date)
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                </span>
            @endif
        </div>
    </div>
</div>