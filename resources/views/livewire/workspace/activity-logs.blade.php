<div class="space-y-6">
    <!-- Top Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-zinc-950 via-indigo-950 to-zinc-900 p-6 md:p-8 text-white shadow-xl border border-indigo-500/25">
        <div class="absolute -right-12 -top-12 size-64 rounded-full bg-indigo-500/15 blur-3xl pointer-events-none animate-float-slow"></div>
        <div class="absolute right-32 -bottom-16 size-48 rounded-full bg-violet-500/15 blur-2xl pointer-events-none animate-float-slow" style="animation-delay: -3s;"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    <svg class="size-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>ISO 27001 & University MIS Compliance Logs</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                    <span>Audit & Activity Trails</span>
                    <span class="text-xs font-mono px-2.5 py-1 rounded-lg bg-white/10 text-zinc-300 font-normal">v2.5 Telemetry</span>
                </h1>
                <p class="text-sm text-indigo-200/80 max-w-2xl">
                    Immutable chronological audit log capturing all task updates, ticket status transitions, assignments, comments, attachments, and resolution events across <strong class="text-white">{{ $workspace->name }}</strong>.
                </p>
            </div>

            <!-- Action Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <a 
                    href="{{ route('workspace.activity-logs.export', ['workspace' => $workspace->slug]) }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white border border-white/20 shadow-sm backdrop-blur-md transition-all cursor-pointer hover-lift"
                >
                    <svg class="size-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export Audit Trail (CSV)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Telemetry Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Activities -->
        <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs hover:border-indigo-400 dark:hover:border-zinc-700 transition-all hover-lift space-y-1">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Recorded Events</span>
                <span class="p-2.5 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ number_format($totalActivities) }}</div>
            <p class="text-[11px] text-zinc-400">Total lifecycle operations recorded</p>
        </div>

        <!-- Ticket Events -->
        <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs hover:border-violet-400 dark:hover:border-zinc-700 transition-all hover-lift space-y-1">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Ticket Lifecycle Logs</span>
                <span class="p-2.5 rounded-2xl bg-violet-500/10 text-violet-600 dark:text-violet-400">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-violet-600 dark:text-violet-400">{{ number_format($totalTicketActivities) }}</div>
            <p class="text-[11px] text-zinc-400">Triage, assignments, resolution logs</p>
        </div>

        <!-- Task Events -->
        <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs hover:border-sky-400 dark:hover:border-zinc-700 transition-all hover-lift space-y-1">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Task Work Logs</span>
                <span class="p-2.5 rounded-2xl bg-sky-500/10 text-sky-600 dark:text-sky-400">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-sky-600 dark:text-sky-400">{{ number_format($totalTaskActivities) }}</div>
            <p class="text-[11px] text-zinc-400">Sprint moves, checklists, time logged</p>
        </div>

        <!-- Today Velocity -->
        <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs hover:border-emerald-400 dark:hover:border-zinc-700 transition-all hover-lift space-y-1">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Logged Today</span>
                <span class="p-2.5 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($todayActivities) }}</div>
            <p class="text-[11px] text-zinc-400">Today's real-time team velocity</p>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <!-- Search -->
            <div class="col-span-1 sm:col-span-2 lg:col-span-4 relative">
                <svg class="size-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 dark:text-zinc-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                    type="text" 
                    wire:model.live.debounce.350ms="search" 
                    placeholder="Search by ticket #, task title, user, action..." 
                    class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 pl-9 pr-3 py-2 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors shadow-2xs"
                    style="color-scheme: light dark;"
                />
            </div>

            <!-- Type Filter -->
            <div class="col-span-1 lg:col-span-2">
                <select 
                    wire:model.live="typeFilter" 
                    class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 py-2 px-3 text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors shadow-2xs cursor-pointer"
                    style="color-scheme: light dark;"
                >
                    <option value="all" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">⚡ All Entities</option>
                    <option value="tickets" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">🎫 Tickets Only</option>
                    <option value="tasks" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">📋 Tasks Only</option>
                </select>
            </div>

            <!-- Action Filter -->
            <div class="col-span-1 lg:col-span-2">
                <select 
                    wire:model.live="actionFilter" 
                    class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 py-2 px-3 text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors shadow-2xs cursor-pointer"
                    style="color-scheme: light dark;"
                >
                    <option value="all" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">🏷️ All Actions</option>
                    <option value="status" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">🔄 Status Transitions</option>
                    <option value="assign" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">👤 Assignments</option>
                    <option value="priority" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">🚨 Priority Changes</option>
                    <option value="comment" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">💬 Comments & Notes</option>
                    <option value="attachment" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">📎 File Attachments</option>
                    <option value="time" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">⏱️ Time Logged</option>
                    <option value="convert" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">🔁 Ticket ⇄ Task Converts</option>
                    <option value="csat" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">⭐ Satisfaction Ratings</option>
                </select>
            </div>

            <!-- User Filter -->
            <div class="col-span-1 lg:col-span-2">
                <select 
                    wire:model.live="selectedUserId" 
                    class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 py-2 px-3 text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors shadow-2xs cursor-pointer"
                    style="color-scheme: light dark;"
                >
                    <option value="" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">👥 All Users</option>
                    @foreach($workspaceUsers as $u)
                        <option value="{{ $u->id }}" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Period Filter -->
            <div class="col-span-1 lg:col-span-2">
                <select 
                    wire:model.live="dateRange" 
                    class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 py-2 px-3 text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors shadow-2xs cursor-pointer"
                    style="color-scheme: light dark;"
                >
                    <option value="all" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">📅 All Time</option>
                    <option value="today" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">Today</option>
                    <option value="7days" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">Last 7 Days</option>
                    <option value="30days" class="bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">Last 30 Days</option>
                </select>
            </div>
        </div>

        @if($search || $typeFilter !== 'all' || $actionFilter !== 'all' || $selectedUserId || $dateRange !== 'all')
            <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800 text-xs">
                <span class="text-zinc-500">Filtered results active</span>
                <button 
                    wire:click="resetFilters" 
                    class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer"
                >
                    Reset all filters
                </button>
            </div>
        @endif
    </div>

    <!-- Audit Log Feed List -->
    <div class="rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-between">
            <h2 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live Workspace Telemetry Stream</span>
            </h2>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                Showing {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} events
            </span>
        </div>

        <div class="divide-y divide-zinc-200/70 dark:divide-zinc-800/70">
            @forelse($logs as $log)
                <div class="p-4 sm:p-5 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    
                    <!-- Left: User Avatar & Main Info -->
                    <div class="flex items-start gap-3.5 min-w-0">
                        <img 
                            src="{{ $log['user'] ? $log['user']->avatar() : 'https://ui-avatars.com/api/?name=System&background=6366f1&color=fff' }}" 
                            alt="{{ $log['user']?->name ?? 'System' }}" 
                            class="size-10 rounded-full ring-2 ring-zinc-200 dark:ring-zinc-700 shrink-0 object-cover mt-0.5"
                        />

                        <div class="space-y-1 min-w-0">
                            <!-- Top metadata line -->
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-bold text-zinc-900 dark:text-white">{{ $log['user']?->name ?? 'System Automated' }}</span>

                                <!-- Action Badge -->
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-{{ $log['badge_color'] }}-500/10 text-{{ $log['badge_color'] }}-600 dark:text-{{ $log['badge_color'] }}-400 border border-{{ $log['badge_color'] }}-500/20">
                                    {{ str_replace('_', ' ', $log['action']) }}
                                </span>

                                <!-- Entity Badge -->
                                @if($log['type'] === 'ticket')
                                    <button 
                                        wire:click="$dispatch('open-ticket-detail', { ticketId: {{ $log['entity_id'] }} })"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-violet-50 dark:bg-violet-950/60 text-violet-700 dark:text-violet-300 border border-violet-200 dark:border-violet-800 hover:bg-violet-100 cursor-pointer hover-lift-sm"
                                        title="Click to view ticket details"
                                    >
                                        <span>🎫 {{ $log['reference'] }}</span>
                                    </button>
                                @else
                                    <button 
                                        wire:click="$dispatch('open-task-detail', { taskId: {{ $log['entity_id'] }} })"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 cursor-pointer hover-lift-sm"
                                        title="Click to view task details"
                                    >
                                        <span>📋 {{ $log['reference'] }}</span>
                                    </button>
                                @endif

                                <span class="text-zinc-400 text-[11px] truncate max-w-[200px] sm:max-w-xs">
                                    "{{ $log['title'] }}"
                                </span>
                            </div>

                            <!-- Description / Diff content -->
                            <div class="text-xs text-zinc-700 dark:text-zinc-300 font-medium">
                                {{ $log['description'] }}
                            </div>

                            <!-- Value transition diff badges if present -->
                            @if($log['from_value'] || $log['to_value'])
                                <div class="flex items-center gap-1.5 text-[11px] pt-0.5">
                                    @if($log['from_value'])
                                        <span class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 line-through">
                                            {{ $log['from_value'] }}
                                        </span>
                                        <svg class="size-3 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                    @endif
                                    <span class="px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-semibold border border-indigo-200/60 dark:border-indigo-800/60">
                                        {{ $log['to_value'] }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Timestamp & Inspect Button -->
                    <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto shrink-0 gap-2">
                        <div class="text-right">
                            <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                                {{ $log['created_at']->diffForHumans() }}
                            </div>
                            <div class="text-[10px] text-zinc-400 font-mono">
                                {{ $log['created_at']->format('M j, Y • h:i A') }}
                            </div>
                        </div>

                        <button 
                            wire:click="inspectLog('{{ $log['type'] }}', {{ $log['id'] }})"
                            type="button" 
                            class="px-3 py-1.5 rounded-xl text-[11px] font-semibold text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-zinc-800 transition-all cursor-pointer border border-zinc-200 dark:border-zinc-700 hover-lift-sm"
                        >
                            Inspect Audit
                        </button>
                    </div>

                </div>
            @empty
                <div class="py-16 text-center space-y-3">
                    <div class="size-14 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                        <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">No activity records found</h3>
                    <p class="text-xs text-zinc-500 max-w-sm mx-auto">
                        There are no audit events matching your search or filters. Try adjusting your parameters.
                    </p>
                    <button wire:click="resetFilters" class="text-xs font-bold text-indigo-600 hover:underline">
                        Reset Filters
                    </button>
                </div>
            @endforelse
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800/80">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- ISO Audit Inspection Slide-over Modal -->
    @if($showInspectModal && $inspectData)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-150 space-y-4 p-6">
                
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Audit Entry #{{ $inspectData['id'] }}</h3>
                            <p class="text-[11px] text-zinc-400 font-mono">{{ $inspectData['type'] }}</p>
                        </div>
                    </div>

                    <button wire:click="closeInspectModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <!-- Entity reference -->
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 space-y-1">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Affected Record</span>
                        <div class="flex items-center justify-between">
                            <strong class="text-zinc-900 dark:text-white font-mono">{{ $inspectData['reference'] }}</strong>
                            <span class="text-zinc-500">{{ $inspectData['title'] }}</span>
                        </div>
                    </div>

                    <!-- Actor details -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Initiated By</span>
                            <strong class="text-zinc-900 dark:text-white">{{ $inspectData['actor'] }}</strong>
                            <div class="text-[10px] text-zinc-400">{{ $inspectData['actor_email'] }}</div>
                        </div>

                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Action Identifier</span>
                            <strong class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $inspectData['action'] }}</strong>
                            <div class="text-[10px] text-zinc-400">{{ $inspectData['relative_time'] }}</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 space-y-1">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Log Summary</span>
                        <div class="text-zinc-800 dark:text-zinc-200">{{ $inspectData['description'] }}</div>
                    </div>

                    <!-- State Transition -->
                    @if($inspectData['from_value'] || $inspectData['to_value'])
                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 space-y-1">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">State Transition Diff</span>
                            <div class="grid grid-cols-2 gap-2 pt-1 font-mono text-[11px]">
                                <div class="p-2 rounded bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-900">
                                    <span class="text-[9px] block text-red-500 font-sans uppercase">Previous Value:</span>
                                    {{ $inspectData['from_value'] ?? '(null / unassigned)' }}
                                </div>
                                <div class="p-2 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900">
                                    <span class="text-[9px] block text-emerald-500 font-sans uppercase">Committed Value:</span>
                                    {{ $inspectData['to_value'] ?? '(null)' }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Timestamp -->
                    <div class="text-[11px] text-zinc-500 flex items-center justify-between pt-1">
                        <span>ISO Timestamp:</span>
                        <span class="font-mono">{{ $inspectData['timestamp'] }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex justify-end">
                    <button 
                        wire:click="closeInspectModal" 
                        type="button" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 cursor-pointer"
                    >
                        Dismiss
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Slide-over Ticket and Task modals for direct inspection -->
    <livewire:tickets.ticket-detail />
    <livewire:tasks.task-detail-modal />
</div>
