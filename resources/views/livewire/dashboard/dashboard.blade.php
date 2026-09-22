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

    <!-- ========================================================================= -->
    <!-- INTERACTIVE PROJECT INFLUX & COMPARATIVE ANALYTICS HUB                     -->
    <!-- ========================================================================= -->
    <div class="space-y-6">
        
        <!-- Interactive Time-Travel & Analytics Toolbar -->
        <div class="rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/90 p-5 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <!-- Left Title & Period Indicator -->
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </span>
                        <h2 class="text-base font-black text-zinc-900 dark:text-white tracking-tight">
                            Interactive Influx &amp; Workload Analytics
                        </h2>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 flex flex-wrap items-center gap-1.5">
                        <span>Comparing</span>
                        <strong class="text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-1.5 py-0.2 rounded border border-indigo-200 dark:border-indigo-800/60 font-semibold">
                            {{ $timeBoundaries['label'] }}
                        </strong>
                        <span>vs previous period</span>
                        <strong class="text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800/80 px-1.5 py-0.2 rounded border border-zinc-200 dark:border-zinc-700 font-medium">
                            {{ $timeBoundaries['prev_label'] }}
                        </strong>
                    </p>
                </div>

                <!-- Right Controls: Filter Pills, Selectors, View Toggles -->
                <div class="flex flex-wrap items-center gap-2.5">
                    
                    <!-- Time Granularity Switcher Pills -->
                    <div class="inline-flex items-center p-1 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/80 text-xs">
                        <button 
                            wire:click="setTimeRange('month')"
                            class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $timeRange === 'month' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}"
                        >
                            Month-Wise
                        </button>
                        <button 
                            wire:click="setTimeRange('last_month')"
                            class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $timeRange === 'last_month' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}"
                        >
                            Last Month
                        </button>
                        <button 
                            wire:click="setTimeRange('year')"
                            class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $timeRange === 'year' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}"
                        >
                            Year-Wise
                        </button>
                        <button 
                            wire:click="setTimeRange('all')"
                            class="px-3 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $timeRange === 'all' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}"
                        >
                            12 Months
                        </button>
                    </div>

                    <!-- Year Selector -->
                    <select 
                        wire:model.live="selectedYear"
                        class="text-xs font-semibold rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5 shadow-xs focus:ring-1 focus:ring-indigo-500"
                        title="Select Year"
                    >
                        @foreach([2024, 2025, 2026, 2027] as $yr)
                            <option value="{{ $yr }}">{{ $yr }}</option>
                        @endforeach
                    </select>

                    <!-- Month Selector (when in month view) -->
                    @if(in_array($timeRange, ['month', 'last_month']))
                        <select 
                            wire:model.live="selectedMonth"
                            class="text-xs font-semibold rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5 shadow-xs focus:ring-1 focus:ring-indigo-500"
                            title="Select Month"
                        >
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->format('M (F)') }}
                                </option>
                            @endfor
                        </select>
                    @endif

                    <!-- Project Filter Dropdown -->
                    <select 
                        wire:model.live="filterProjectId"
                        class="text-xs font-semibold rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5 shadow-xs focus:ring-1 focus:ring-indigo-500 max-w-[170px] truncate"
                        title="Filter by Project"
                    >
                        <option value="">All Projects Scope</option>
                        @foreach($workspaceProjects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>

                    <!-- Chart Style Toggle (Line vs Bar) -->
                    <div class="inline-flex items-center p-1 rounded-xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/80 text-xs">
                        <button 
                            wire:click="setChartType('line')"
                            class="p-1.5 rounded-lg transition-colors cursor-pointer {{ $chartType === 'line' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                            title="Line Curve View"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </button>
                        <button 
                            wire:click="setChartType('bar')"
                            class="p-1.5 rounded-lg transition-colors cursor-pointer {{ $chartType === 'bar' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                            title="Bar Columns View"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Comparative Velocity & Influx KPI Cards (4 Delta Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Tickets Influx Delta -->
            <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/90 shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Incoming Ticket Influx</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $ticketCreationDelta['current'] }}</span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-bold px-1.5 py-0.5 rounded-md {{ $ticketCreationDelta['pct'] > 0 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : ($ticketCreationDelta['pct'] < 0 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500') }}">
                                @if($ticketCreationDelta['pct'] > 0)
                                    &uarr; +{{ $ticketCreationDelta['pct'] }}%
                                @elseif($ticketCreationDelta['pct'] < 0)
                                    &darr; {{ $ticketCreationDelta['pct'] }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="size-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <span>Prior period: <strong class="text-zinc-800 dark:text-zinc-200">{{ $ticketCreationDelta['previous'] }}</strong></span>
                    <span>Net: <strong class="{{ $ticketCreationDelta['delta'] >= 0 ? 'text-amber-500' : 'text-emerald-500' }}">{{ $ticketCreationDelta['delta'] > 0 ? '+' : '' }}{{ $ticketCreationDelta['delta'] }}</strong></span>
                </div>
            </div>

            <!-- 2. Tickets Resolved Delta -->
            <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/90 shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Tickets Resolved</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $ticketResolutionDelta['current'] }}</span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-bold px-1.5 py-0.5 rounded-md {{ $ticketResolutionDelta['pct'] >= 0 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' }}">
                                @if($ticketResolutionDelta['pct'] > 0)
                                    &uarr; +{{ $ticketResolutionDelta['pct'] }}%
                                @elseif($ticketResolutionDelta['pct'] < 0)
                                    &darr; {{ $ticketResolutionDelta['pct'] }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="size-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <span>Prior period: <strong class="text-zinc-800 dark:text-zinc-200">{{ $ticketResolutionDelta['previous'] }}</strong></span>
                    <span>Net: <strong class="{{ $ticketResolutionDelta['delta'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">{{ $ticketResolutionDelta['delta'] > 0 ? '+' : '' }}{{ $ticketResolutionDelta['delta'] }}</strong></span>
                </div>
            </div>

            <!-- 3. Tasks Created Delta -->
            <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/90 shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Tasks Created</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $taskCreationDelta['current'] }}</span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-bold px-1.5 py-0.5 rounded-md {{ $taskCreationDelta['pct'] >= 0 ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500' }}">
                                @if($taskCreationDelta['pct'] > 0)
                                    &uarr; +{{ $taskCreationDelta['pct'] }}%
                                @elseif($taskCreationDelta['pct'] < 0)
                                    &darr; {{ $taskCreationDelta['pct'] }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="size-10 rounded-2xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <span>Prior period: <strong class="text-zinc-800 dark:text-zinc-200">{{ $taskCreationDelta['previous'] }}</strong></span>
                    <span>Net: <strong class="text-sky-500">{{ $taskCreationDelta['delta'] > 0 ? '+' : '' }}{{ $taskCreationDelta['delta'] }}</strong></span>
                </div>
            </div>

            <!-- 4. Tasks Completed Delta -->
            <div class="p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/90 shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Tasks Completed</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $taskCompletionDelta['current'] }}</span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-bold px-1.5 py-0.5 rounded-md {{ $taskCompletionDelta['pct'] >= 0 ? 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' }}">
                                @if($taskCompletionDelta['pct'] > 0)
                                    &uarr; +{{ $taskCompletionDelta['pct'] }}%
                                @elseif($taskCompletionDelta['pct'] < 0)
                                    &darr; {{ $taskCompletionDelta['pct'] }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="size-10 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <span>Prior period: <strong class="text-zinc-800 dark:text-zinc-200">{{ $taskCompletionDelta['previous'] }}</strong></span>
                    <span>Net: <strong class="{{ $taskCompletionDelta['delta'] >= 0 ? 'text-teal-500' : 'text-rose-500' }}">{{ $taskCompletionDelta['delta'] > 0 ? '+' : '' }}{{ $taskCompletionDelta['delta'] }}</strong></span>
                </div>
            </div>

        </div>

        <!-- Interactive Visual Analytics Charts (Playable Canvases) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Chart 1 (2 Columns): Velocity Curve: Tickets vs Tasks Trend -->
            <div class="lg:col-span-2 p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs flex flex-col justify-between space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-indigo-500"></span>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Tickets vs Tasks Inflow &amp; Velocity Trend</h3>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Click any legend item to toggle datasets. Hover over points for exact counts.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-[11px] text-zinc-400">
                        <span class="inline-flex items-center gap-1">
                            <span class="size-2 rounded-full bg-indigo-500"></span> Tickets Influx
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="size-2 rounded-full bg-emerald-500"></span> Resolved
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="size-2 rounded-full bg-sky-400"></span> Tasks Created
                        </span>
                    </div>
                </div>

                <!-- Canvas Wrapper -->
                <div 
                    class="h-72 w-full relative"
                    x-data="{
                        chart: null,
                        init() {
                            this.buildChart();
                        },
                        buildChart() {
                            if (this.chart) this.chart.destroy();
                            const ctx = this.$refs.canvas.getContext('2d');
                            this.chart = new Chart(ctx, {
                                type: '{{ $chartType }}',
                                data: {
                                    labels: @json($chartLabels),
                                    datasets: [
                                        {
                                            label: 'Tickets Influx',
                                            data: @json($chartTicketsCreated),
                                            borderColor: '#6366f1',
                                            backgroundColor: '{{ $chartType === 'line' ? 'rgba(99, 102, 241, 0.12)' : 'rgba(99, 102, 241, 0.85)' }}',
                                            fill: true,
                                            tension: 0.35,
                                            borderWidth: 2.5,
                                            pointRadius: 4,
                                            pointHoverRadius: 6,
                                        },
                                        {
                                            label: 'Tickets Resolved',
                                            data: @json($chartTicketsResolved),
                                            borderColor: '#10b981',
                                            backgroundColor: '{{ $chartType === 'line' ? 'rgba(16, 185, 129, 0.12)' : 'rgba(16, 185, 129, 0.85)' }}',
                                            fill: true,
                                            tension: 0.35,
                                            borderWidth: 2.5,
                                            pointRadius: 4,
                                            pointHoverRadius: 6,
                                        },
                                        {
                                            label: 'Tasks Created',
                                            data: @json($chartTasksCreated),
                                            borderColor: '#38bdf8',
                                            backgroundColor: '{{ $chartType === 'line' ? 'rgba(56, 189, 248, 0.1)' : 'rgba(56, 189, 248, 0.85)' }}',
                                            fill: false,
                                            borderDash: [4, 4],
                                            tension: 0.35,
                                            borderWidth: 2,
                                            pointRadius: 3,
                                            pointHoverRadius: 5,
                                        },
                                        {
                                            label: 'Tasks Completed',
                                            data: @json($chartTasksCompleted),
                                            borderColor: '#14b8a6',
                                            backgroundColor: '{{ $chartType === 'line' ? 'rgba(20, 184, 166, 0.1)' : 'rgba(20, 184, 166, 0.85)' }}',
                                            fill: false,
                                            tension: 0.35,
                                            borderWidth: 2,
                                            pointRadius: 3,
                                            pointHoverRadius: 5,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            labels: {
                                                color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#52525b',
                                                font: { size: 11, weight: '600' },
                                                usePointStyle: true,
                                                boxWidth: 8,
                                                padding: 14,
                                            }
                                        },
                                        tooltip: {
                                            padding: 12,
                                            cornerRadius: 10,
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                color: document.documentElement.classList.contains('dark') ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                                            },
                                            ticks: {
                                                color: document.documentElement.classList.contains('dark') ? '#71717a' : '#a1a1aa',
                                                font: { size: 10 }
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: {
                                                color: document.documentElement.classList.contains('dark') ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                                            },
                                            ticks: {
                                                color: document.documentElement.classList.contains('dark') ? '#71717a' : '#a1a1aa',
                                                font: { size: 10 },
                                                precision: 0
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }"
                    wire:key="velocity-trend-canvas-{{ $timeRange }}-{{ $selectedYear }}-{{ $selectedMonth }}-{{ $filterProjectId }}-{{ $chartType }}"
                >
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>

            <!-- Chart 2: Issue Category Breakdown Doughnut -->
            <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-purple-500"></span>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Tickets by Category Breakdown</h3>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                        Categorical distribution for {{ $timeBoundaries['label'] }}.
                    </p>
                </div>

                <div 
                    class="h-72 w-full relative flex items-center justify-center"
                    x-data="{
                        chart: null,
                        init() {
                            const ctx = this.$refs.canvas.getContext('2d');
                            const data = @json($chartCategoryData);
                            const hasData = data && data.some(v => v > 0);
                            
                            this.chart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: @json($chartCategoryLabels),
                                    datasets: [{
                                        data: hasData ? data : [1],
                                        backgroundColor: hasData ? [
                                            '#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#14b8a6', '#f43f5e'
                                        ] : ['#3f3f46'],
                                        borderWidth: 2,
                                        borderColor: document.documentElement.classList.contains('dark') ? '#18181b' : '#ffffff',
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#52525b',
                                                font: { size: 10, weight: '600' },
                                                boxWidth: 8,
                                                padding: 8
                                            }
                                        }
                                    },
                                    cutout: '62%'
                                }
                            });
                        }
                    }"
                    wire:key="category-breakdown-canvas-{{ $timeRange }}-{{ $selectedYear }}-{{ $selectedMonth }}-{{ $filterProjectId }}"
                >
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>

        </div>

        <!-- Chart 3: Project Workload & Influx Matrix (Horizontal Bar Comparison) -->
        <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-blue-500"></span>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Project Workload vs Ticket Influx Comparison</h3>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                        Highlights which project is carrying high task backlogs vs incoming ticket volume.
                    </p>
                </div>
                <span class="text-xs font-semibold text-zinc-400">
                    {{ count($chartProjectNames) }} Projects Tracked
                </span>
            </div>

            <div 
                class="h-64 w-full relative"
                x-data="{
                    chart: null,
                    init() {
                        const ctx = this.$refs.canvas.getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: @json($chartProjectNames),
                                datasets: [
                                    {
                                        label: 'Active/Total Tasks',
                                        data: @json($chartProjectTasks),
                                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                        borderColor: '#3b82f6',
                                        borderWidth: 1,
                                        borderRadius: 6,
                                    },
                                    {
                                        label: 'Incoming Tickets',
                                        data: @json($chartProjectTickets),
                                        backgroundColor: 'rgba(168, 85, 247, 0.8)',
                                        borderColor: '#a855f7',
                                        borderWidth: 1,
                                        borderRadius: 6,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#52525b',
                                            font: { size: 11, weight: '600' }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            color: document.documentElement.classList.contains('dark') ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                                        },
                                        ticks: {
                                            color: document.documentElement.classList.contains('dark') ? '#e4e4e7' : '#27272a',
                                            font: { size: 11, weight: '500' }
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: document.documentElement.classList.contains('dark') ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                                        },
                                        ticks: {
                                            precision: 0,
                                            color: document.documentElement.classList.contains('dark') ? '#71717a' : '#a1a1aa',
                                            font: { size: 10 }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }"
                wire:key="project-matrix-bar-canvas-{{ $timeRange }}-{{ $selectedYear }}-{{ $selectedMonth }}-{{ $filterProjectId }}"
            >
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <!-- Project Deep-Dive Matrix & Issue Category MoM Comparison Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Project Deep-Dive Matrix Table (2 Columns) -->
            <div class="lg:col-span-2 p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Project Workload &amp; Influx Breakdown</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Month-over-month influx comparisons per project.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-zinc-100 dark:border-zinc-800 text-[10px] uppercase font-bold text-zinc-400 tracking-wider">
                                <th class="pb-3 font-bold">Project</th>
                                <th class="pb-3 font-bold text-center">Tasks (Done/Total)</th>
                                <th class="pb-3 font-bold text-center">Tickets ({{ $timeBoundaries['label'] }})</th>
                                <th class="pb-3 font-bold text-center">Tickets ({{ $timeBoundaries['prev_label'] }})</th>
                                <th class="pb-3 font-bold text-center">MoM Trend</th>
                                <th class="pb-3 font-bold text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                            @forelse($projectsMatrix as $row)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="size-2 rounded-full" style="background-color: {{ $row['space_color'] }}"></span>
                                            <div>
                                                <span class="font-bold text-zinc-900 dark:text-white block">{{ $row['project_name'] }}</span>
                                                <span class="text-[10px] text-zinc-400">{{ $row['space_name'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $row['completed_tasks'] }}/{{ $row['total_tasks'] }}</span>
                                        <div class="h-1.5 w-16 mx-auto bg-zinc-100 dark:bg-zinc-800 rounded-full mt-1 overflow-hidden">
                                            <div 
                                                class="h-full bg-indigo-500 rounded-full"
                                                style="width: {{ $row['total_tasks'] > 0 ? round(($row['completed_tasks'] / $row['total_tasks']) * 100) : 0 }}%"
                                            ></div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-200 dark:border-indigo-800/40">
                                            {{ $row['tickets_count_current'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="font-medium text-zinc-500">
                                            {{ $row['tickets_count_previous'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="text-[11px] font-bold {{ $row['ticket_delta'] > 0 ? 'text-amber-500' : ($row['ticket_delta'] < 0 ? 'text-emerald-500' : 'text-zinc-400') }}">
                                            {{ $row['ticket_delta'] > 0 ? '+' : '' }}{{ $row['ticket_delta'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        @if($row['open_tickets'] > 3)
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                                High Attention
                                            </span>
                                        @elseif($row['tickets_count_current'] > 0)
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                                Active Influx
                                            </span>
                                        @else
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                Stable
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-zinc-400">No projects found for current filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Issue Category MoM Comparison Matrix Table (1 Column) -->
            <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Issue Category MoM Comparison</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Changes in issue types vs previous period.</p>
                </div>

                <div class="space-y-3">
                    @forelse($categoriesMatrix as $cat)
                        <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/80 space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-zinc-900 dark:text-white">{{ $cat['name'] }}</span>
                                <span class="text-[11px] font-bold {{ $cat['delta'] > 0 ? 'text-amber-500' : ($cat['delta'] < 0 ? 'text-emerald-500' : 'text-zinc-400') }}">
                                    {{ $cat['delta'] > 0 ? '+' : '' }}{{ $cat['delta'] }} ({{ $cat['pct'] > 0 ? '+' : '' }}{{ $cat['pct'] }}%)
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-zinc-400">
                                <span>This period: <strong class="text-indigo-600 dark:text-indigo-400">{{ $cat['current'] }}</strong></span>
                                <span>Prior: <strong class="text-zinc-600 dark:text-zinc-300">{{ $cat['previous'] }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-zinc-400 text-center py-6">No ticket categories logged.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Gemini AI Trend Diagnostics & Interactive Query Assistant -->
        <div class="p-6 rounded-3xl bg-gradient-to-r from-purple-500/10 via-indigo-500/10 to-transparent border border-purple-500/20 dark:border-purple-500/30 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-2xl bg-purple-600 text-white shadow-xs">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                            <span>Gemini AI Analytics Diagnostics</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-purple-500/20 text-purple-600 dark:text-purple-400">AI Powered</span>
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Ask questions or generate automated trend diagnosis across projects, ticket spikes, and velocity.
                        </p>
                    </div>
                </div>

                <button 
                    wire:click="generateAiAnalyticsInsight"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-xs transition-all cursor-pointer shrink-0 disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="generateAiAnalyticsInsight">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="generateAiAnalyticsInsight" class="animate-spin size-4 border-2 border-white border-t-transparent rounded-full"></span>
                    <span>Diagnose Trends with AI</span>
                </button>
            </div>

            <!-- Custom AI Query Prompt Input -->
            <form wire:submit="generateAiAnalyticsInsight" class="flex gap-2">
                <input 
                    type="text" 
                    wire:model="aiPromptQuery" 
                    placeholder="Ask Gemini anything about this data (e.g. 'Why did tickets spike?', 'Which project has the highest risk?')..."
                    class="flex-1 text-xs px-3.5 py-2.5 rounded-xl bg-white dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-hidden focus:ring-1 focus:ring-purple-500"
                />
                <button 
                    type="submit" 
                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 transition-colors cursor-pointer"
                >
                    Ask
                </button>
            </form>

            <!-- Rendered AI Insight -->
            @if($aiAnalyticsInsight)
                <div class="p-5 rounded-2xl bg-white/80 dark:bg-zinc-900/80 border border-purple-500/30 text-xs text-zinc-800 dark:text-zinc-200 leading-relaxed space-y-2 animate-in fade-in zoom-in-98 duration-200">
                    <div class="flex items-center justify-between pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <span class="font-bold text-purple-600 dark:text-purple-400">Gemini Trend Insights</span>
                        <button wire:click="$set('aiAnalyticsInsight', null)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs cursor-pointer">
                            &times; Clear
                        </button>
                    </div>
                    <div class="prose dark:prose-invert max-w-none text-xs leading-relaxed">
                        {!! Str::markdown($aiAnalyticsInsight) !!}
                    </div>
                </div>
            @endif
        </div>

    </div>

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
