<div 
    class="p-4 flex items-center justify-between gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 cursor-pointer transition-colors group"
    wire:click="$dispatch('open-task-detail', { taskId: {{ $task->id }} })"
>
    <div class="flex items-center gap-3 min-w-0 flex-1">
        <!-- Toggle complete checkbox -->
        <button 
            wire:click.stop="toggleComplete({{ $task->id }})" 
            type="button" 
            class="size-5 rounded border border-zinc-300 dark:border-zinc-700 flex items-center justify-center text-white transition-colors {{ $task->isDone() ? 'bg-emerald-500 border-emerald-500' : 'hover:border-indigo-500' }}"
        >
            @if($task->isDone())
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            @endif
        </button>

        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate {{ $task->isDone() ? 'line-through text-zinc-400 dark:text-zinc-500' : '' }}">
                {{ $task->title }}
            </p>
            <div class="flex items-center gap-2 text-[10px] text-zinc-400 mt-0.5">
                <span>{{ $task->taskList?->project?->space?->name }}</span>
                <span>/</span>
                <span>{{ $task->taskList?->project?->name }}</span>
                @if($task->checklists->count() > 0)
                    <span>•</span>
                    <span>{{ $task->checklistStats()['completed'] }}/{{ $task->checklistStats()['total'] }} items</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Badges -->
    <div class="flex items-center gap-3 shrink-0">
        @php $b = $task->priorityBadge(); @endphp
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $b['bg'] }} {{ $b['text'] }}">
            {{ $b['label'] }}
        </span>

        <span class="text-xs font-semibold px-2 py-0.5 rounded-md" style="background-color: {{ $task->status?->color }}20; color: {{ $task->status?->color }}">
            {{ $task->status?->name }}
        </span>

        @if($task->due_date)
            <span class="text-[11px] {{ $task->isOverdue() ? 'text-rose-500 font-semibold' : 'text-zinc-400' }}">
                {{ $task->due_date->format('M j') }}
            </span>
        @endif
    </div>
</div>
