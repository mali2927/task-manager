<div class="space-y-6 pb-12">
    
    <!-- Top Dashboard Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white flex items-center gap-2.5">
                <span>{{ $workspace->name }} Overview</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                    Live
                </span>
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Real-time team throughput, workload distribution, and AI intelligence.</p>
        </div>

        <!-- Action Controls (Space Filter, CSV Export, Gemini Reports) -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Space Filter -->
            <select wire:model.live="selectedSpaceId" class="text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 py-1.5 px-3">
                <option value="">All Spaces</option>
                @foreach($spaces as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                @endforeach
            </select>

            <!-- Export CSV -->
            <button 
                wire:click="exportTasksCsv" 
                type="button" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 transition-colors"
                title="Download CSV export"
            >
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                <span>Export CSV</span>
            </button>

            <!-- Gemini AI Standup Generator -->
            <button 
                wire:click="generateStandup" 
                type="button" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 border border-purple-500/30 transition-all cursor-pointer"
            >
                <span wire:loading.remove wire:target="generateStandup">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </span>
                <span wire:loading wire:target="generateStandup" class="animate-spin size-3.5 border-2 border-purple-500 border-t-transparent rounded-full"></span>
                <span>My Standup</span>
            </button>

            <!-- Gemini AI Team Summary -->
            <button 
                wire:click="generateTeamAiSummary" 
                type="button" 
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-all cursor-pointer"
            >
                <span wire:loading.remove wire:target="generateTeamAiSummary">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </span>
                <span wire:loading wire:target="generateTeamAiSummary" class="animate-spin size-3.5 border-2 border-white border-t-transparent rounded-full"></span>
                <span>AI Team Report</span>
            </button>
        </div>
    </div>

    <!-- AI Briefing Modal/Card (if active) -->
    @if($teamAiSummary)
        <div class="p-5 rounded-2xl border border-indigo-200 dark:border-indigo-900/60 bg-gradient-to-r from-indigo-50/70 to-purple-50/70 dark:from-indigo-950/30 dark:to-purple-950/30 shadow-sm relative">
            <div class="flex items-center justify-between pb-3 border-b border-indigo-200/50 dark:border-indigo-800/50 mb-3">
                <div class="flex items-center gap-2 font-bold text-sm text-indigo-900 dark:text-indigo-200">
                    <span class="p-1 rounded-md bg-indigo-600 text-white">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                    </span>
                    <span>Executive Team Digest (Generated by Gemini)</span>
                </div>
                <button wire:click="$set('teamAiSummary', null)" class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">&times; Close</button>
            </div>
            <div class="text-xs text-zinc-800 dark:text-zinc-200 prose dark:prose-invert max-w-none leading-relaxed">
                {!! Str::markdown($teamAiSummary) !!}
            </div>
        </div>
    @endif

    @if($myStandupReport)
        <div class="p-5 rounded-2xl border border-purple-200 dark:border-purple-900/60 bg-purple-50/60 dark:bg-purple-950/30 shadow-sm relative">
            <div class="flex items-center justify-between pb-3 border-b border-purple-200/50 dark:border-purple-800/50 mb-3">
                <div class="flex items-center gap-2 font-bold text-sm text-purple-900 dark:text-purple-200">
                    <span>Agile Standup Summary</span>
                </div>
                <button wire:click="$set('myStandupReport', null)" class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">&times; Close</button>
            </div>
            <div class="text-xs text-zinc-800 dark:text-zinc-200 prose dark:prose-invert max-w-none leading-relaxed">
                {!! Str::markdown($myStandupReport) !!}
            </div>
        </div>
    @endif

    <!-- 1. PERSONAL KPI SUMMARY CARDS -->
    <div>
        <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3">My Pending Tasks</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            
            <!-- Open Tasks -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Open</span>
                    <p class="text-2xl font-black text-zinc-900 dark:text-white mt-0.5">{{ $myOpen }}</p>
                </div>
                <div class="size-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
            </div>

            <!-- Overdue Tasks -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs flex items-center justify-between {{ $myOverdue > 0 ? 'ring-1 ring-rose-500/30' : '' }}">
                <div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Overdue</span>
                    <p class="text-2xl font-black {{ $myOverdue > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-900 dark:text-white' }} mt-0.5">
                        {{ $myOverdue }}
                    </p>
                </div>
                <div class="size-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
            </div>

            <!-- Due Today -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Due Today</span>
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ $myDueToday }}</p>
                </div>
                <div class="size-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
            </div>

            <!-- Due This Week -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Due This Week</span>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $myDueThisWeek }}</p>
                </div>
                <div class="size-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TEAM DELIVERY SUMMARY & STATUS DISTRIBUTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Throughput Card -->
        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs flex flex-col justify-between space-y-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Completion Rate</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-4xl font-black text-zinc-900 dark:text-white">{{ $completionRate }}%</span>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $completedTasks }} of {{ $totalTasks }} shipped</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="h-3 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $completionRate }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] text-zinc-400">
                    <span>Sprint Goal</span>
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $completionRate >= 70 ? 'On Track' : 'Needs Velocity' }}</span>
                </div>
            </div>

            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs">
                <span class="text-zinc-500">Active Blockers:</span>
                <span class="font-bold {{ $totalBlocked > 0 ? 'text-red-500' : 'text-zinc-700 dark:text-zinc-300' }}">{{ $totalBlocked }} tasks</span>
            </div>
        </div>

        <!-- Status Breakdown Pills & Bars -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-4">
            <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Task Status Distribution</span>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2">
                @foreach($statusCounts as $name => $data)
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800/80 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="size-2 rounded-full" style="background-color: {{ $data['color'] }}"></span>
                            <span class="text-[11px] font-semibold text-zinc-600 dark:text-zinc-400 truncate">{{ $name }}</span>
                        </div>
                        <p class="text-xl font-bold text-zinc-900 dark:text-white">{{ $data['count'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Visual Bar Segment -->
            <div class="h-2.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden flex">
                @foreach($statusCounts as $name => $data)
                    @php $pct = $totalTasks > 0 ? ($data['count'] / $totalTasks) * 100 : 0; @endphp
                    @if($pct > 0)
                        <div class="h-full transition-all" style="width: {{ $pct }}%; background-color: {{ $data['color'] }}" title="{{ $name }}: {{ $data['count'] }}"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- 3. TEAM WORKLOAD CAPACITY VIEW -->
    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Team Workload & Capacity</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Track active task distribution to prevent team burnout or under-allocation.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
            @foreach($members as $member)
                @php 
                    $taskCount = $member->assigned_tasks_count; 
                    $capacityPercent = min(100, (int) round(($taskCount / 5) * 100)); // assuming 5 active tasks is 100% capacity
                @endphp
                <div class="p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/40 space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $member->avatar() }}" class="size-9 rounded-full ring-2 ring-white dark:ring-zinc-700" alt="" />
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-zinc-900 dark:text-white truncate">{{ $member->name }}</p>
                            <p class="text-[10px] text-zinc-400 truncate">{{ $member->pivot->job_title ?? 'Team Member' }}</p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-zinc-500">Active Tasks:</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $taskCount }}</span>
                        </div>
                        <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div 
                                class="h-full rounded-full {{ $taskCount >= 4 ? 'bg-amber-500' : ($taskCount >= 6 ? 'bg-red-500' : 'bg-indigo-500') }}" 
                                style="width: {{ $capacityPercent }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. BOTTOM SPLIT: OVERDUE ALERTS & REAL-TIME ACTIVITY FEED -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Overdue Tasks Report -->
        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="size-2 rounded-full bg-rose-500 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Overdue & Urgent Tasks</h3>
                </div>
                <span class="text-xs text-rose-500 font-semibold">{{ $overdueList->count() }} items</span>
            </div>

            <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                @forelse($overdueList as $task)
                    <div 
                        wire:click="$dispatch('open-task-detail', { taskId: {{ $task->id }} })"
                        class="py-3 flex items-center justify-between gap-3 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/40 px-2 rounded-lg transition-colors group"
                    >
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-zinc-900 dark:text-white group-hover:text-indigo-600 truncate">{{ $task->title }}</p>
                            <div class="flex items-center gap-2 text-[10px] text-zinc-400 mt-0.5">
                                <span>{{ $task->taskList?->project?->name }}</span>
                                <span>•</span>
                                <span class="text-rose-500 font-medium">Due: {{ $task->due_date?->format('M j') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[10px] px-2 py-0.5 rounded font-semibold bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                                {{ $task->status?->name }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-zinc-400">
                        🎉 Great job! No overdue tasks in this scope.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Live Activity Audit Feed -->
        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Workspace Activity Stream</h3>

            <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                @forelse($recentActivities as $act)
                    <div class="flex items-start gap-3 text-xs">
                        <img src="{{ $act->user ? $act->user->avatar() : 'https://ui-avatars.com/api/?name=System' }}" class="size-6 rounded-full mt-0.5 shrink-0" alt="" />
                        <div class="min-w-0 flex-1">
                            <p class="text-zinc-800 dark:text-zinc-200 leading-snug">
                                <strong class="text-zinc-900 dark:text-white">{{ $act->user?->name ?? 'System' }}</strong>
                                {{ $act->description }}
                            </p>
                            <span class="text-[10px] text-zinc-400 mt-0.5 block">{{ $act->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-zinc-400">
                        No activity recorded yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Embedded Task Detail Slide-Over Modal -->
    <livewire:tasks.task-detail-modal />
</div>
