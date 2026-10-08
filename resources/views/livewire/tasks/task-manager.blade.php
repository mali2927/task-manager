<div class="h-full flex flex-col -m-6 bg-zinc-50/50 dark:bg-zinc-950 min-h-screen">
    
    <!-- Top Workspace Action & Navigation Bar -->
    <div class="border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-6 py-3.5 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-30 shadow-xs">
        
        <!-- Left: Breadcrumb & Current Context -->
        <div class="flex items-center gap-2 text-sm">
            <span class="font-semibold text-zinc-900 dark:text-white flex items-center gap-1.5">
                <span class="size-2.5 rounded-full bg-indigo-500"></span>
                {{ $workspace->name }}
            </span>
            <span class="text-zinc-400">/</span>

            @if($selectedSpaceId)
                @php $activeSpace = $spaces->firstWhere('id', $selectedSpaceId); @endphp
                <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $activeSpace?->name }}</span>
            @else
                <span class="text-zinc-500 dark:text-zinc-400">All Spaces</span>
            @endif
        </div>

        <!-- Middle: View Switcher Tabs -->
        <div class="flex items-center p-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/80 text-xs font-medium">
            <button 
                wire:click="setView('board')" 
                type="button" 
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all {{ $activeView === 'board' ? 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-xs font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}"
            >
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                <span>Board</span>
            </button>

            <button 
                wire:click="setView('list')" 
                type="button" 
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all {{ $activeView === 'list' ? 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-xs font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}"
            >
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                <span>List</span>
            </button>

            <button 
                wire:click="setView('calendar')" 
                type="button" 
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all {{ $activeView === 'calendar' ? 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-xs font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}"
            >
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>Calendar</span>
            </button>

            <button 
                wire:click="setView('gantt')" 
                type="button" 
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all {{ $activeView === 'gantt' ? 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-xs font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}"
            >
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                <span>Gantt</span>
            </button>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-3">
            <button 
                wire:click="openCreateModal" 
                type="button" 
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-all cursor-pointer hover:shadow-indigo-500/25"
            >
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>New Task</span>
            </button>
        </div>
    </div>

    <!-- Secondary Filter Bar -->
    <div class="border-b border-zinc-200/80 dark:border-zinc-800/80 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-xs px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 text-xs">
        
        <!-- Left Filters (Search, Space, Priority, Assignee) -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Search -->
            <div class="relative w-48 sm:w-60">
                <svg class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchQuery" 
                    placeholder="Search tasks..." 
                    class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 pl-8 pr-3 py-1.5 text-zinc-800 dark:text-zinc-200 focus:ring-1 focus:ring-indigo-500"
                />
            </div>

            <!-- Space Selector -->
            <select 
                wire:model.live="selectedSpaceId" 
                class="text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 py-1.5 px-2.5"
            >
                <option value="">All Spaces</option>
                @foreach($spaces as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                @endforeach
            </select>

            <!-- Priority Selector -->
            <select 
                wire:model.live="filterPriority" 
                class="text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 py-1.5 px-2.5"
            >
                <option value="">All Priorities</option>
                <option value="urgent">🚨 Urgent</option>
                <option value="high">🔶 High</option>
                <option value="normal">🔷 Normal</option>
                <option value="low">⚪ Low</option>
            </select>

            <!-- Assignee Selector -->
            <select 
                wire:model.live="filterAssignee" 
                class="text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 py-1.5 px-2.5"
            >
                <option value="">All Assignees</option>
                <option value="unassigned">Unassigned</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
            </select>

            @if($searchQuery || $filterPriority || $filterAssignee || $selectedSpaceId)
                <button 
                    wire:click="resetFilters" 
                    type="button" 
                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline px-1"
                >
                    Clear Filters
                </button>
            @endif
        </div>

        <!-- Right Summary Stats -->
        <div class="text-zinc-500 dark:text-zinc-400 text-xs">
            Showing <strong class="text-zinc-800 dark:text-zinc-200">{{ $tasks->count() }}</strong> tasks
        </div>
    </div>

    <!-- MAIN VIEW AREA -->
    <div class="flex-1 p-6 overflow-x-auto">
        
        <!-- 1. KANBAN BOARD VIEW -->
        @if($activeView === 'board')
            <div class="flex items-start gap-4 pb-6 min-w-max">
                @foreach($statuses as $status)
                    @php 
                        $columnTasks = $tasks->where('status_id', $status->id); 
                    @endphp
                    <div class="w-80 shrink-0 flex flex-col rounded-2xl bg-zinc-100/80 dark:bg-zinc-900/60 border border-zinc-200/70 dark:border-zinc-800/80 max-h-[calc(100vh-210px)]">
                        
                        <!-- Column Header -->
                        <div class="p-3.5 border-b border-zinc-200/60 dark:border-zinc-800/60 flex items-center justify-between shrink-0">
                            <div class="flex items-center gap-2">
                                <span class="size-2.5 rounded-full" style="background-color: {{ $status->color }}"></span>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200">{{ $status->name }}</h3>
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                    {{ $columnTasks->count() }}
                                </span>
                            </div>

                            <button 
                                wire:click="openCreateModal({{ $status->id }})" 
                                class="p-1 rounded-md text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 transition-colors"
                                title="Add task to {{ $status->name }}"
                            >
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            </button>
                        </div>

                        <!-- Column Cards (Scrollable) -->
                        <div class="flex-1 overflow-y-auto p-3 space-y-3">
                            @forelse($columnTasks as $task)
                                <div 
                                    wire:click="$dispatch('open-task-detail', { taskId: {{ $task->id }} })"
                                    class="p-3.5 rounded-xl bg-white dark:bg-zinc-800/90 border border-zinc-200/80 dark:border-zinc-700/60 shadow-xs hover:shadow-md hover:border-indigo-400/50 dark:hover:border-indigo-500/50 transition-all cursor-pointer group space-y-2.5"
                                >
                                    <!-- Top Card Row: Priority & Space -->
                                    <div class="flex items-center justify-between gap-2">
                                        @php $badge = $task->priorityBadge(); @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }}">
                                            {{ $badge['label'] }}
                                        </span>

                                        <span class="text-[10px] text-zinc-400 truncate max-w-[140px]">
                                            {{ $task->taskList?->project?->name }}
                                        </span>
                                    </div>

                                    <!-- Task Title -->
                                    <h4 class="text-xs font-semibold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                        {{ $task->title }}
                                    </h4>

                                    <!-- Tags -->
                                    @if($task->tags->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($task->tags->take(3) as $tag)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-zinc-100 dark:bg-zinc-700/60 text-zinc-600 dark:text-zinc-300">
                                                    #{{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Card Footer: Checklist, Due Date, Assignees -->
                                    <div class="pt-2 border-t border-zinc-100 dark:border-zinc-700/50 flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400">
                                        
                                        <div class="flex items-center gap-2.5">
                                            <!-- Checklists count -->
                                            @if($task->checklistStats()['total'] > 0)
                                                <span class="flex items-center gap-1 {{ $task->checklistStats()['percent'] === 100 ? 'text-emerald-500 font-medium' : '' }}">
                                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    {{ $task->checklistStats()['completed'] }}/{{ $task->checklistStats()['total'] }}
                                                </span>
                                            @endif

                                            <!-- Due date -->
                                            @if($task->due_date)
                                                <span class="flex items-center gap-1 {{ $task->isOverdue() ? 'text-red-500 font-semibold' : '' }}">
                                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                    {{ $task->due_date->format('M j') }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Assignees Avatars -->
                                        <div class="flex -space-x-1.5 overflow-hidden">
                                            @foreach($task->assignees->take(3) as $assignee)
                                                <img class="inline-block size-5 rounded-full ring-2 ring-white dark:ring-zinc-800" src="{{ $assignee->avatar() }}" title="{{ $assignee->name }}" alt="" />
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center text-xs text-zinc-400 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl">
                                    No tasks in {{ $status->name }}
                                </div>
                            @endforelse
                        </div>

                        <!-- Column Quick Add Footer -->
                        <div class="p-2 border-t border-zinc-200/60 dark:border-zinc-800/60 shrink-0">
                            <button 
                                wire:click="openCreateModal({{ $status->id }})" 
                                class="w-full py-1.5 px-3 rounded-lg text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/70 flex items-center justify-center gap-1 transition-colors"
                            >
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                <span>Add Task</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- 2. TABLE / LIST VIEW -->
        @if($activeView === 'list')
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-xs">
                @foreach($statuses as $status)
                    @php $statusTasks = $tasks->where('status_id', $status->id); @endphp
                    <div class="border-b border-zinc-200/70 dark:border-zinc-800 last:border-0" x-data="{ open: true }">
                        
                        <!-- Status Group Header -->
                        <div 
                            @click="open = !open" 
                            class="px-5 py-3 bg-zinc-50/80 dark:bg-zinc-800/40 flex items-center justify-between cursor-pointer hover:bg-zinc-100/80 dark:hover:bg-zinc-800/70 select-none transition-colors"
                        >
                            <div class="flex items-center gap-2.5">
                                <span class="size-2.5 rounded-full" style="background-color: {{ $status->color }}"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">{{ $status->name }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                                    {{ $statusTasks->count() }}
                                </span>
                            </div>

                            <svg class="size-4 text-zinc-400 transition-transform" :class="{ 'rotate-180': !open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>

                        <!-- Table -->
                        <div x-show="open" class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800 text-zinc-400 uppercase text-[10px] tracking-wider">
                                        <th class="py-2.5 px-5 font-medium">Task Title</th>
                                        <th class="py-2.5 px-4 font-medium">Priority</th>
                                        <th class="py-2.5 px-4 font-medium">Due Date</th>
                                        <th class="py-2.5 px-4 font-medium">Assignees</th>
                                        <th class="py-2.5 px-4 font-medium">Progress</th>
                                        <th class="py-2.5 px-4 font-medium">Project</th>
                                        <th class="py-2.5 px-4 font-medium text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                                    @forelse($statusTasks as $task)
                                        <tr 
                                            class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 cursor-pointer transition-colors group"
                                            wire:click="$dispatch('open-task-detail', { taskId: {{ $task->id }} })"
                                        >
                                            <td class="py-3 px-5 font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                                <div class="flex items-center gap-2">
                                                    <span>{{ $task->title }}</span>
                                                    @if($task->tags->count() > 0)
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-500">
                                                            #{{ $task->tags->first()->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="py-3 px-4">
                                                @php $b = $task->priorityBadge(); @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $b['bg'] }} {{ $b['text'] }}">
                                                    {{ $b['label'] }}
                                                </span>
                                            </td>

                                            <td class="py-3 px-4 text-zinc-500 dark:text-zinc-400 whitespace-nowrap {{ $task->isOverdue() ? 'text-red-500 font-semibold' : '' }}">
                                                {{ $task->due_date ? $task->due_date->format('M j, Y') : '-' }}
                                            </td>

                                            <td class="py-3 px-4">
                                                <div class="flex -space-x-1 overflow-hidden">
                                                    @forelse($task->assignees as $assignee)
                                                        <img class="inline-block size-5 rounded-full ring-2 ring-white dark:ring-zinc-900" src="{{ $assignee->avatar() }}" title="{{ $assignee->name }}" alt="" />
                                                    @empty
                                                        <span class="text-zinc-400 text-[11px]">-</span>
                                                    @endforelse
                                                </div>
                                            </td>

                                            <td class="py-3 px-4">
                                                @if($task->checklistStats()['total'] > 0)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-14 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $task->checklistStats()['percent'] }}%"></div>
                                                        </div>
                                                        <span class="text-[10px] text-zinc-500">{{ $task->checklistStats()['completed'] }}/{{ $task->checklistStats()['total'] }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-zinc-400 text-[11px]">-</span>
                                                @endif
                                            </td>

                                            <td class="py-3 px-4 text-zinc-500 dark:text-zinc-400 truncate max-w-[140px]">
                                                {{ $task->taskList?->project?->name }}
                                            </td>

                                            <td class="py-3 px-4 text-right" @click.stop>
                                                <select 
                                                    wire:change="updateTaskStatus({{ $task->id }}, $event.target.value)" 
                                                    class="text-[11px] rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 py-1 px-2"
                                                >
                                                    @foreach($statuses as $st)
                                                        <option value="{{ $st->id }}" {{ $st->id == $task->status_id ? 'selected' : '' }}>
                                                            {{ $st->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-zinc-400">No tasks in {{ $status->name }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- 3. CALENDAR VIEW -->
        @if($activeView === 'calendar')
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 space-y-4 shadow-xs">
                
                <!-- Month Navigator Header -->
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($currentMonth . '-01')->format('F Y') }}
                    </h3>

                    <div class="flex items-center gap-1.5">
                        <button wire:click="prevMonth" class="p-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <button wire:click="$set('currentMonth', '{{ now()->format('Y-m') }}')" class="px-3 py-1 text-xs font-semibold rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                            Today
                        </button>
                        <button wire:click="nextMonth" class="p-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                    <!-- Day names -->
                    <div class="grid grid-cols-7 bg-zinc-50 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-800 text-center text-xs font-semibold text-zinc-500 py-2">
                        <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
                    </div>

                    <!-- Day cells -->
                    <div class="grid grid-cols-7 divide-x divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($calendarDays as $day)
                            <div class="min-h-[110px] p-2 {{ $day['is_current_month'] ? 'bg-white dark:bg-zinc-900' : 'bg-zinc-50/50 dark:bg-zinc-950/50 text-zinc-400' }} flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold {{ $day['is_today'] ? 'size-6 rounded-full bg-indigo-600 text-white flex items-center justify-center' : 'text-zinc-700 dark:text-zinc-300' }}">
                                        {{ $day['date']->format('j') }}
                                    </span>
                                </div>

                                <div class="mt-1 space-y-1 overflow-y-auto max-h-20">
                                    @foreach($day['tasks'] as $t)
                                        <div 
                                            wire:click="$dispatch('open-task-detail', { taskId: {{ $t->id }} })"
                                            class="p-1 rounded text-[10px] font-medium truncate cursor-pointer hover:opacity-80 transition-opacity"
                                            style="background-color: {{ $t->status?->color }}25; color: {{ $t->status?->color }}; border-left: 2px solid {{ $t->status?->color }}"
                                            title="{{ $t->title }}"
                                        >
                                            {{ $t->title }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- 4. GANTT / TIMELINE VIEW -->
        @if($activeView === 'gantt')
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 space-y-6 shadow-xs">
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Project Gantt & Timeline</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Visualize task durations, start and due dates, and cross-task dependencies.</p>
                </div>

                <div class="space-y-4">
                    @foreach($tasks as $t)
                        <div 
                            wire:click="$dispatch('open-task-detail', { taskId: {{ $t->id }} })"
                            class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-indigo-500/50 cursor-pointer transition-all space-y-2 bg-zinc-50/50 dark:bg-zinc-800/40"
                        >
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ $t->title }}</span>
                                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                                    {{ $t->start_date ? $t->start_date->format('M j') : 'No start' }} → {{ $t->due_date ? $t->due_date->format('M j, Y') : 'No due' }}
                                </span>
                            </div>

                            <!-- Duration visualization bar -->
                            <div class="relative h-6 bg-zinc-200 dark:bg-zinc-700/80 rounded-lg overflow-hidden flex items-center px-3">
                                <div 
                                    class="absolute inset-y-0 left-0 rounded-lg opacity-85" 
                                    style="width: {{ $t->checklistStats()['total'] > 0 ? max(15, $t->checklistStats()['percent']) : ($t->isDone() ? 100 : 45) }}%; background-color: {{ $t->status?->color }}"
                                ></div>
                                <span class="relative z-10 text-[10px] font-bold text-white uppercase drop-shadow-xs">
                                    {{ $t->status?->name }} ({{ $t->priority }})
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- Quick Create Task Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-150">
                <form wire:submit.prevent="createTask">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Create New Task</h3>
                            
                            <!-- Gemini AI Assist button -->
                            <button 
                                wire:click="askAiForSuggestions" 
                                type="button" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 cursor-pointer"
                                title="Let Gemini suggest priority and tags based on title"
                            >
                                <span wire:loading.remove wire:target="askAiForSuggestions">✨ AI Suggest</span>
                                <span wire:loading wire:target="askAiForSuggestions" class="animate-spin size-3 border-2 border-indigo-500 border-t-transparent rounded-full"></span>
                            </button>
                        </div>

                        <!-- Title -->
                        <div>
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Task Title</label>
                            <input 
                                type="text" 
                                wire:model="newTaskTitle" 
                                required
                                placeholder="e.g. Implement OAuth login with GitHub" 
                                class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-zinc-900 dark:text-white focus:ring-indigo-500"
                            />
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Description (Optional)</label>
                            <textarea 
                                wire:model="newTaskDescription" 
                                rows="3" 
                                placeholder="Describe requirements, acceptance criteria, or context..." 
                                class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-zinc-900 dark:text-white focus:ring-indigo-500"
                            ></textarea>
                        </div>

                        <!-- Project List & Status -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Target List</label>
                                <select wire:model="newTaskListId" class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5">
                                    @foreach($spaces as $sp)
                                        <optgroup label="{{ $sp->name }}">
                                            @foreach($sp->projects as $pr)
                                                @foreach($pr->lists as $ls)
                                                    <option value="{{ $ls->id }}">{{ $pr->name }} &gt; {{ $ls->name }}</option>
                                                @endforeach
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Status</label>
                                <select wire:model="newTaskStatusId" class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5">
                                    @foreach($statuses as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Priority & Due Date -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Priority</label>
                                <select wire:model="newTaskPriority" class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5">
                                    <option value="urgent">🚨 Urgent</option>
                                    <option value="high">🔶 High</option>
                                    <option value="normal">🔷 Normal</option>
                                    <option value="low">⚪ Low</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Due Date</label>
                                <input type="date" wire:model="newTaskDueDate" class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5" />
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-2">
                        <button wire:click="$set('showCreateModal', false)" type="button" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Embedded Task Detail Slide-Over Modal -->
    <livewire:tasks.task-detail-modal />
</div>
