<div class="space-y-6 pb-16">
    
    <!-- Top Executive Header & Context Bar -->
    <div class="rounded-3xl bg-gradient-to-br from-white via-zinc-50/50 to-indigo-50/30 dark:from-zinc-900 dark:via-zinc-900/90 dark:to-indigo-950/20 border border-zinc-200/80 dark:border-zinc-800/90 p-6 shadow-xs relative overflow-hidden">
        <!-- Background Ambient Glow -->
        <div class="absolute -top-24 -right-24 size-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 size-72 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <!-- Greeting & Quick Stats -->
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                        <span class="size-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                        Live Workspace
                    </span>
                    <span class="text-xs text-zinc-400 dark:text-zinc-500">•</span>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ now()->format('l, F j, Y') }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                    <span>{{ $greeting }}, {{ auth()->user()->name }}</span>
                    <span class="text-2xl">👋</span>
                </h1>

                <!-- Workspace Quick Metrics Pills -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800/70 border border-zinc-200/60 dark:border-zinc-700/60 text-xs text-zinc-600 dark:text-zinc-300">
                        <svg class="size-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        <span><strong class="text-zinc-900 dark:text-white">{{ $spacesProgress->count() }}</strong> Spaces</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800/70 border border-zinc-200/60 dark:border-zinc-700/60 text-xs text-zinc-600 dark:text-zinc-300">
                        <svg class="size-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        <span><strong class="text-zinc-900 dark:text-white">{{ $totalTasks }}</strong> Tasks</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800/70 border border-zinc-200/60 dark:border-zinc-700/60 text-xs text-zinc-600 dark:text-zinc-300">
                        <svg class="size-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                        <span><strong class="text-zinc-900 dark:text-white">{{ $ticketsOpen }}</strong> Open Tickets</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800/70 border border-zinc-200/60 dark:border-zinc-700/60 text-xs text-zinc-600 dark:text-zinc-300">
                        <svg class="size-3.5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <span><strong class="text-zinc-900 dark:text-white">{{ $members->count() }}</strong> Team Members</span>
                    </div>
                </div>
            </div>

            <!-- Header Controls & Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <!-- Space Filter -->
                <div class="relative w-full sm:w-auto">
                    <select 
                        wire:model.live="selectedSpaceId" 
                        class="w-full sm:w-auto text-xs font-medium rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-2 px-3.5 shadow-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden"
                    >
                        <option value="">All Spaces Scope</option>
                        @foreach($spaces as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- AI Quick Actions -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button 
                        wire:click="generateStandup" 
                        type="button" 
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 border border-purple-500/30 transition-all cursor-pointer shadow-xs"
                        title="Generate personalized daily standup with Google Gemini"
                    >
                        <span wire:loading.remove wire:target="generateStandup">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </span>
                        <span wire:loading wire:target="generateStandup" class="animate-spin size-3.5 border-2 border-purple-500 border-t-transparent rounded-full"></span>
                        <span>My Standup</span>
                    </button>

                    <button 
                        wire:click="generateTeamAiSummary" 
                        type="button" 
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs transition-all cursor-pointer"
                        title="Generate executive team briefing with Google Gemini"
                    >
                        <span wire:loading.remove wire:target="generateTeamAiSummary">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </span>
                        <span wire:loading wire:target="generateTeamAiSummary" class="animate-spin size-3.5 border-2 border-white border-t-transparent rounded-full"></span>
                        <span>AI Executive Brief</span>
                    </button>
                </div>

                <!-- Export CSV Dropdown -->
                <div class="flex items-center gap-1">
                    <button 
                        wire:click="exportTasksCsv" 
                        type="button" 
                        class="p-2 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 transition-colors"
                        title="Export Tasks CSV"
                    >
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    </button>

                    <button 
                        wire:click="exportTicketsCsv" 
                        type="button" 
                        class="p-2 rounded-xl text-xs font-medium bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 transition-colors"
                        title="Export Tickets CSV"
                    >
                        <svg class="size-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Focus Mode Switcher Tabs -->
        <div class="mt-6 pt-4 border-t border-zinc-200/60 dark:border-zinc-800/80 flex items-center justify-between gap-4">
            <div class="inline-flex items-center p-1 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/80 text-xs">
                <button 
                    wire:click="setTab('overview')"
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $activeTab === 'overview' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}"
                >
                    Unified Overview
                </button>
                <button 
                    wire:click="setTab('tasks')"
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $activeTab === 'tasks' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}"
                >
                    Tasks &amp; Delivery
                </button>
                <button 
                    wire:click="setTab('tickets')"
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $activeTab === 'tickets' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}"
                >
                    Support &amp; Helpdesk
                </button>
            </div>

            <!-- Direct Quick Links -->
            <div class="hidden sm:flex items-center gap-3 text-xs">
                <a href="{{ route('workspace.tasks', ['workspace' => $workspace->slug]) }}" class="font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors" wire:navigate>
                    Task Board &rarr;
                </a>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <a href="{{ route('workspace.tickets.queue', ['workspace' => $workspace->slug]) }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>
                    Triage Queue &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- AI Briefing Cards (if active) -->
    @if($teamAiSummary)
        <div class="p-6 rounded-3xl border border-indigo-200 dark:border-indigo-900/60 bg-gradient-to-r from-indigo-50/70 via-purple-50/40 to-white dark:from-indigo-950/40 dark:via-purple-950/20 dark:to-zinc-900 shadow-sm relative animate-in fade-in zoom-in-98 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-indigo-200/50 dark:border-indigo-800/50 mb-4">
                <div class="flex items-center gap-2.5 font-bold text-sm text-indigo-950 dark:text-indigo-200">
                    <span class="p-1.5 rounded-lg bg-indigo-600 text-white shadow-xs">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </span>
                    <span>Gemini AI Executive Digest</span>
                </div>
                <button wire:click="$set('teamAiSummary', null)" class="text-xs px-2.5 py-1 rounded-lg bg-indigo-100/50 hover:bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 cursor-pointer">
                    &times; Dismiss
                </button>
            </div>
            <div class="text-xs text-zinc-800 dark:text-zinc-200 prose dark:prose-invert max-w-none leading-relaxed">
                {!! Str::markdown($teamAiSummary) !!}
            </div>
        </div>
    @endif

    @if($myStandupReport)
        <div class="p-6 rounded-3xl border border-purple-200 dark:border-purple-900/60 bg-gradient-to-r from-purple-50/70 via-indigo-50/40 to-white dark:from-purple-950/40 dark:via-indigo-950/20 dark:to-zinc-900 shadow-sm relative animate-in fade-in zoom-in-98 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-purple-200/50 dark:border-purple-800/50 mb-4">
                <div class="flex items-center gap-2.5 font-bold text-sm text-purple-950 dark:text-purple-200">
                    <span class="p-1.5 rounded-lg bg-purple-600 text-white shadow-xs">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </span>
                    <span>Gemini Daily Standup Briefing</span>
                </div>
                <button wire:click="$set('myStandupReport', null)" class="text-xs px-2.5 py-1 rounded-lg bg-purple-100/50 hover:bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 cursor-pointer">
                    &times; Dismiss
                </button>
            </div>
            <div class="text-xs text-zinc-800 dark:text-zinc-200 prose dark:prose-invert max-w-none leading-relaxed">
                {!! Str::markdown($myStandupReport) !!}
            </div>
        </div>
    @endif

    <!-- 1. HERO EXECUTIVE KPI CARDS (4 Hero Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Delivery Velocity & Completion -->
        <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/90 shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Sprint Delivery Velocity</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $completionRate }}%</span>
                        <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded-md">
                            {{ $completionRate >= 50 ? 'On Track' : 'In Flight' }}
                        </span>
                    </div>
                </div>
                <div class="size-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-700" style="width: {{ $completionRate }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] text-zinc-500 dark:text-zinc-400">
                    <span>{{ $completedTasks }} of {{ $totalTasks }} tasks shipped</span>
                    @if($totalBlocked > 0)
                        <span class="text-rose-500 font-semibold">{{ $totalBlocked }} Blocked</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Personal Focus & My Open Tasks -->
        <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/90 shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">My Pending Tasks</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $myOpen }}</span>
                        @if($myOverdue > 0)
                            <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400 bg-rose-500/10 px-1.5 py-0.5 rounded-md">
                                {{ $myOverdue }} Overdue
                            </span>
                        @else
                            <span class="text-[11px] font-medium text-blue-600 dark:text-blue-400 bg-blue-500/10 px-1.5 py-0.5 rounded-md">
                                Active
                            </span>
                        @endif
                    </div>
                </div>
                <div class="size-10 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                <span>Today: <strong class="text-amber-500">{{ $myDueToday }}</strong></span>
                <span>•</span>
                <span>This Week: <strong class="text-zinc-800 dark:text-zinc-200">{{ $myDueThisWeek }}</strong></span>
                <span>•</span>
                <span>Assigned Tickets: <strong class="text-indigo-500">{{ $ticketsAssignedToMe }}</strong></span>
            </div>
        </div>

        <!-- 3. Support Helpdesk & SLA Health -->
        <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/90 shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Support &amp; SLA Compliance</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $slaComplianceRate }}%</span>
                        <span class="text-[11px] font-bold {{ $ticketsOverdue > 0 ? 'text-rose-600 dark:text-rose-400 bg-rose-500/10' : 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10' }} px-1.5 py-0.5 rounded-md">
                            {{ $ticketsOverdue > 0 ? $ticketsOverdue . ' Breached' : '100% Target' }}
                        </span>
                    </div>
                </div>
                <div class="size-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                <span>In Triage: <strong class="text-zinc-900 dark:text-white">{{ $ticketsOpen }}</strong></span>
                <span>•</span>
                <span>In Progress: <strong class="text-zinc-900 dark:text-white">{{ $ticketsInProgress }}</strong></span>
                <span>•</span>
                <span>Avg: <strong class="text-emerald-500">{{ $avgResolutionTime }}</strong></span>
            </div>
        </div>

        <!-- 4. Operational Risk & Attention Radar -->
        <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border {{ $totalAttentionItems > 0 ? 'border-amber-500/30 dark:border-amber-500/20 bg-amber-500/5' : 'border-zinc-200/80 dark:border-zinc-800/90' }} shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Platform Risk Radar</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-black {{ $totalAttentionItems > 0 ? 'text-amber-500' : 'text-emerald-500' }}">
                            {{ $totalAttentionItems }}
                        </span>
                        <span class="text-[11px] font-bold {{ $totalAttentionItems > 0 ? 'text-amber-600 dark:text-amber-400 bg-amber-500/10' : 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10' }} px-1.5 py-0.5 rounded-md">
                            {{ $totalAttentionItems > 0 ? 'Items Require Action' : 'All Clear' }}
                        </span>
                    </div>
                </div>
                <div class="size-10 rounded-2xl {{ $totalAttentionItems > 0 ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400' : 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400' }} flex items-center justify-center">
                    @if($totalAttentionItems > 0)
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    @else
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                <span>Overdue Tasks: <strong class="{{ $totalOverdue > 0 ? 'text-rose-500' : '' }}">{{ $totalOverdue }}</strong></span>
                <span>•</span>
                <span>Breached SLAs: <strong class="{{ $ticketsOverdue > 0 ? 'text-rose-500' : '' }}">{{ $ticketsOverdue }}</strong></span>
                <span>•</span>
                <span>Blocked: <strong class="{{ $totalBlocked > 0 ? 'text-rose-500' : '' }}">{{ $totalBlocked }}</strong></span>
            </div>
        </div>
    </div>

    <!-- 2. URGENT ATTENTION CENTER (When items require action) -->
    @if($criticalTasks->isNotEmpty() || $urgentTickets->isNotEmpty())
        <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="size-2 rounded-full bg-rose-500 animate-ping"></span>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Needs Immediate Attention</h3>
                </div>
                <span class="text-xs text-zinc-400">Click any item to view or resolve immediately</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Critical Tasks -->
                <div class="space-y-2">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 flex items-center justify-between">
                        <span>Critical Tasks ({{ $criticalTasks->count() }})</span>
                        <a href="{{ route('workspace.tasks', ['workspace' => $workspace->slug]) }}" class="hover:underline text-indigo-500" wire:navigate>View Board &rarr;</a>
                    </div>

                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80 rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 px-3">
                        @forelse($criticalTasks as $task)
                            <div 
                                wire:click="$dispatch('open-task-detail', { taskId: {{ $task->id }} })"
                                class="py-2.5 flex items-center justify-between gap-3 cursor-pointer hover:bg-zinc-100/60 dark:hover:bg-zinc-800/60 -mx-1 px-2 rounded-xl transition-colors group"
                            >
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-zinc-900 dark:text-white group-hover:text-indigo-500 truncate">{{ $task->title }}</p>
                                    <div class="flex items-center gap-2 text-[10px] text-zinc-400 mt-0.5">
                                        <span>{{ $task->taskList?->project?->name }}</span>
                                        <span>•</span>
                                        <span class="{{ $task->isOverdue() ? 'text-rose-500 font-bold' : '' }}">
                                            {{ $task->due_date ? 'Due ' . $task->due_date->format('M j') : 'No due date' }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold {{ $task->isOverdue() ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400' : 'bg-amber-500/10 text-amber-500' }}">
                                    {{ $task->status?->name }}
                                </span>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-zinc-400">Zero critical task blockers.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Urgent Tickets -->
                <div class="space-y-2">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 flex items-center justify-between">
                        <span>Urgent / Breached Tickets ({{ $urgentTickets->count() }})</span>
                        <a href="{{ route('workspace.tickets.queue', ['workspace' => $workspace->slug]) }}" class="hover:underline text-indigo-500" wire:navigate>Triage Queue &rarr;</a>
                    </div>

                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80 rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 px-3">
                        @forelse($urgentTickets as $tick)
                            <div 
                                wire:click="$dispatch('open-ticket-detail', { ticketId: {{ $tick->id }} })"
                                class="py-2.5 flex items-center justify-between gap-3 cursor-pointer hover:bg-zinc-100/60 dark:hover:bg-zinc-800/60 -mx-1 px-2 rounded-xl transition-colors group"
                            >
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-mono font-bold text-indigo-500">{{ $tick->ticket_number }}</span>
                                        <p class="text-xs font-semibold text-zinc-900 dark:text-white group-hover:text-indigo-500 truncate">{{ $tick->title }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-zinc-400 mt-0.5">
                                        <span>{{ $tick->category?->name ?? 'General' }}</span>
                                        <span>•</span>
                                        <span>By {{ $tick->raisedBy?->name }}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold {{ $tick->isOverdue() ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' : 'bg-amber-500/10 text-amber-500' }}">
                                    {{ ucfirst($tick->priority) }} SLA
                                </span>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-zinc-400">All support tickets within SLA parameters.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 3. SPACES & PROJECTS HEALTH MATRIX (Active in 'overview' or 'tasks' tabs) -->
    @if(in_array($activeTab, ['overview', 'tasks']))
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <svg class="size-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        <span>Spaces &amp; Projects Delivery Matrix</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Real-time throughput and execution status across departmental spaces.</p>
                </div>
                <a href="{{ route('workspace.tasks', ['workspace' => $workspace->slug]) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>
                    Browse All Projects &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($spacesProgress as $sp)
                    <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs hover:shadow-md hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="size-3 rounded-full" style="background-color: {{ $sp['color'] }}"></span>
                                    <h4 class="text-sm font-bold text-zinc-900 dark:text-white truncate">{{ $sp['name'] }}</h4>
                                </div>
                                <span class="text-xs font-black text-zinc-900 dark:text-white">{{ $sp['pct'] }}%</span>
                            </div>

                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">
                                {{ $sp['projects_count'] }} {{ Str::plural('Project', $sp['projects_count']) }} • {{ $sp['done'] }}/{{ $sp['total_tasks'] }} Tasks Done
                            </p>
                        </div>

                        <div class="space-y-2">
                            <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                <div 
                                    class="h-full rounded-full transition-all duration-500" 
                                    style="width: {{ $sp['pct'] }}%; background-color: {{ $sp['color'] }}"
                                ></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-zinc-400">
                                <span>Status: {{ $sp['pct'] >= 60 ? 'On Schedule' : 'In Progress' }}</span>
                                <a href="{{ route('workspace.tasks', ['workspace' => $workspace->slug, 'space' => $sp['id']]) }}" class="hover:underline font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white" wire:navigate>
                                    View Board &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. TEAM WORKLOAD & CAPACITY HEATMAP -->
    <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <svg class="size-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span>Combined Team Workload &amp; Capacity Heatmap</span>
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Cross-functional load balancing across sprint tasks and active support tickets.</p>
            </div>
            
            <a href="{{ route('workspace.tickets.capacity', ['workspace' => $workspace->slug]) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>
                Manage Agent Capacities &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1">
            @foreach($members->take(8) as $m)
                <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-800/30 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $m['avatar'] }}" class="size-10 rounded-full ring-2 ring-white dark:ring-zinc-700 object-cover shrink-0" alt="{{ $m['name'] }}" />
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-zinc-900 dark:text-white truncate">{{ $m['name'] }}</p>
                            <p class="text-[10px] text-zinc-400 truncate">{{ $m['job_title'] }}</p>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md uppercase tracking-wider {{ $m['status_color'] === 'emerald' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : ($m['status_color'] === 'yellow' ? 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400') }}">
                            {{ $m['status_label'] }}
                        </span>
                    </div>

                    <!-- Meters -->
                    <div class="space-y-1.5 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-zinc-500">Active Tasks:</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $m['active_tasks'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-zinc-500">Support Tickets:</span>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $m['active_tickets'] }} / {{ $m['capacity_limit'] }}</span>
                        </div>
                        
                        <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div 
                                class="h-full rounded-full transition-all duration-500 {{ $m['ticket_load_pct'] >= 100 ? 'bg-rose-500' : ($m['ticket_load_pct'] >= 80 ? 'bg-amber-500' : 'bg-indigo-500') }}" 
                                style="width: {{ min(100, $m['ticket_load_pct']) }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 5. DUAL DISTRIBUTION MATRICES (Task Statuses & Ticket Priorities) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Task Status Breakdown -->
        <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Task Status Distribution</span>
                <span class="text-xs font-medium text-zinc-500">{{ $totalTasks }} total tasks</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                @foreach($statusCounts as $name => $data)
                    <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/80 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="size-2 rounded-full" style="background-color: {{ $data['color'] }}"></span>
                            <span class="text-[10px] font-semibold text-zinc-600 dark:text-zinc-400 truncate">{{ $name }}</span>
                        </div>
                        <p class="text-lg font-black text-zinc-900 dark:text-white">{{ $data['count'] }}</p>
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

        <!-- Ticket Priority & SLA Matrix -->
        <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Ticket Priority &amp; SLA Breakdown</span>
                <span class="text-xs font-medium text-zinc-500">{{ $totalTicketsCount }} total tickets</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                @foreach($ticketPriorityCounts as $pKey => $pData)
                    <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/80 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="size-2 rounded-full" style="background-color: {{ $pData['color'] }}"></span>
                            <span class="text-[10px] font-semibold text-zinc-600 dark:text-zinc-400 truncate">{{ $pData['label'] }}</span>
                        </div>
                        <p class="text-lg font-black text-zinc-900 dark:text-white">{{ $pData['count'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Stacked Priority Bar -->
            <div class="h-2.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden flex">
                @php $activeTicketTotal = collect($ticketPriorityCounts)->sum('count'); @endphp
                @foreach($ticketPriorityCounts as $pKey => $pData)
                    @php $pPct = $activeTicketTotal > 0 ? ($pData['count'] / $activeTicketTotal) * 100 : 0; @endphp
                    @if($pPct > 0)
                        <div class="h-full transition-all" style="width: {{ $pPct }}%; background-color: {{ $pData['color'] }}" title="{{ $pData['label'] }}: {{ $pData['count'] }}"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- 6. UNIFIED AUDIT ACTIVITY FEED -->
    <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Workspace Real-Time Audit Feed</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Live operational log of all task progress and ticket support lifecycle changes.</p>
            </div>

            <!-- Feed Filter Tabs -->
            <div class="inline-flex items-center p-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/80 text-xs">
                <button 
                    wire:click="setActivityFilter('all')" 
                    class="px-3 py-1 rounded-lg font-semibold transition-all cursor-pointer {{ $activityFilter === 'all' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}"
                >
                    All Events
                </button>
                <button 
                    wire:click="setActivityFilter('tasks')" 
                    class="px-3 py-1 rounded-lg font-semibold transition-all cursor-pointer {{ $activityFilter === 'tasks' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}"
                >
                    Tasks
                </button>
                <button 
                    wire:click="setActivityFilter('tickets')" 
                    class="px-3 py-1 rounded-lg font-semibold transition-all cursor-pointer {{ $activityFilter === 'tickets' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}"
                >
                    Support
                </button>
            </div>
        </div>

        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80 max-h-96 overflow-y-auto pr-1">
            @forelse($recentActivities as $act)
                <div class="py-3 flex items-start gap-3.5 text-xs">
                    <img 
                        src="{{ $act['user'] ? $act['user']->avatar() : 'https://ui-avatars.com/api/?name=System' }}" 
                        class="size-7 rounded-full mt-0.5 shrink-0 object-cover ring-1 ring-zinc-200 dark:ring-zinc-700" 
                        alt="" 
                    />
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-md {{ $act['type'] === 'task' ? 'bg-blue-500/10 text-blue-500' : 'bg-purple-500/10 text-purple-500' }}">
                                {{ strtoupper($act['type']) }}
                            </span>
                            <strong class="text-zinc-900 dark:text-white">{{ $act['user']?->name ?? 'System' }}</strong>
                            <span class="text-zinc-500 dark:text-zinc-400 truncate">{{ $act['description'] }}</span>
                        </div>

                        <div class="flex items-center gap-2 text-[10px] text-zinc-400 mt-1">
                            <span class="font-medium text-zinc-600 dark:text-zinc-300 truncate">{{ $act['title'] }}</span>
                            <span>•</span>
                            <span>{{ $act['created_at']->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-xs text-zinc-400">
                    No recent events found matching your filter.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Embedded Task Detail Slide-Over Modal -->
    <livewire:tasks.task-detail-modal />

    <!-- Embedded Ticket Detail Slide-Over Drawer -->
    <livewire:tickets.ticket-detail />
</div>
