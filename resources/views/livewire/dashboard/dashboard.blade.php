<div class="space-y-6 pb-16">
    
    <!-- Top Executive Header & Command Center Bar -->
    <div class="rounded-3xl bg-gradient-to-br from-indigo-50/90 via-white to-violet-50/60 dark:from-zinc-950 dark:via-indigo-950/70 dark:to-zinc-950 border border-indigo-200/90 dark:border-indigo-500/30 text-slate-900 dark:text-white p-6 sm:p-7 shadow-2xl shadow-indigo-500/5 relative overflow-hidden backdrop-blur-md">
        <!-- Ambient Glow Orbs -->
        <div class="absolute -top-32 -right-32 size-96 bg-indigo-500/15 dark:bg-indigo-500/20 rounded-full blur-3xl pointer-events-none animate-float-slow"></div>
        <div class="absolute -bottom-32 -left-32 size-96 bg-violet-500/15 dark:bg-violet-500/20 rounded-full blur-3xl pointer-events-none animate-float-slow"></div>
        <div class="absolute top-1/2 left-1/3 size-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
            <!-- Left: Greeting, Status, Workspace Branding & Quick Metrics -->
            <div class="space-y-3.5 flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-100/90 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-500/40 shadow-xs backdrop-blur-sm">
                        <span class="size-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-glow-pulse"></span>
                        Executive Operations Hub
                    </span>
                    <span class="text-xs text-indigo-300 dark:text-indigo-300/40">•</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-white/80 dark:bg-zinc-800/80 text-slate-700 dark:text-zinc-300 border border-slate-200/80 dark:border-zinc-700/60 shadow-2xs">
                        <svg class="size-3 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <span>{{ $workspace->name }}</span>
                    </span>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="bg-gradient-to-r from-slate-900 via-indigo-950 to-violet-950 dark:from-white dark:via-indigo-100 dark:to-violet-200 bg-clip-text text-transparent">{{ $greeting }}, {{ auth()->user()->name }}</span>
                        <span class="text-2xl sm:text-3xl animate-bounce">👋</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-1 font-normal">
                        Real-time delivery intelligence, SLA telemetry, and cross-functional capacity across all active spaces.
                    </p>
                </div>

                <!-- Rich Workspace Quick Metric Badges -->
                <div class="flex flex-wrap items-center gap-2.5 pt-1">
                    <div class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/80 dark:bg-zinc-900/80 hover:bg-white dark:hover:bg-zinc-900 border border-indigo-100/80 hover:border-indigo-300 dark:border-indigo-500/20 dark:hover:border-indigo-500/40 text-xs text-slate-600 dark:text-zinc-300 transition-all shadow-2xs backdrop-blur-xs hover-lift">
                        <span class="p-1 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-300 group-hover:scale-110 transition-transform">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </span>
                        <span><strong class="text-slate-900 dark:text-white font-bold text-sm">{{ $spacesProgress->count() }}</strong> Spaces</span>
                    </div>

                    <div class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/80 dark:bg-zinc-900/80 hover:bg-white dark:hover:bg-zinc-900 border border-sky-100/80 hover:border-sky-300 dark:border-sky-500/20 dark:hover:border-sky-500/40 text-xs text-slate-600 dark:text-zinc-300 transition-all shadow-2xs backdrop-blur-xs hover-lift">
                        <span class="p-1 rounded-lg bg-sky-50 dark:bg-sky-500/20 text-sky-600 dark:text-sky-300 group-hover:scale-110 transition-transform">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </span>
                        <span><strong class="text-slate-900 dark:text-white font-bold text-sm">{{ $totalTasks }}</strong> Tasks</span>
                    </div>

                    <div class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/80 dark:bg-zinc-900/80 hover:bg-white dark:hover:bg-zinc-900 border border-amber-100/80 hover:border-amber-300 dark:border-amber-500/20 dark:hover:border-amber-500/40 text-xs text-slate-600 dark:text-zinc-300 transition-all shadow-2xs backdrop-blur-xs hover-lift">
                        <span class="p-1 rounded-lg bg-amber-50 dark:bg-amber-500/20 text-amber-600 dark:text-amber-300 group-hover:scale-110 transition-transform">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                        </span>
                        <span><strong class="text-slate-900 dark:text-white font-bold text-sm">{{ $ticketsOpen }}</strong> Open Tickets</span>
                    </div>

                    <div class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/80 dark:bg-zinc-900/80 hover:bg-white dark:hover:bg-zinc-900 border border-violet-100/80 hover:border-violet-300 dark:border-violet-500/20 dark:hover:border-violet-500/40 text-xs text-slate-600 dark:text-zinc-300 transition-all shadow-2xs backdrop-blur-xs hover-lift">
                        <span class="p-1 rounded-lg bg-violet-50 dark:bg-violet-500/20 text-violet-600 dark:text-violet-300 group-hover:scale-110 transition-transform">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </span>
                        <span><strong class="text-slate-900 dark:text-white font-bold text-sm">{{ $members->count() }}</strong> Team Members</span>
                    </div>
                </div>
            </div>

            <!-- Right: Real-Time Live Clock Card + Action Panel -->
            <div class="flex flex-col sm:flex-row xl:flex-col items-stretch sm:items-center xl:items-end gap-3.5">
                
                <!-- EXECUTIVE REAL-TIME CLOCK WIDGET -->
                <div 
                    x-data="{
                        time: '{{ $currentTime }}',
                        date: '{{ $currentDate }}',
                        tz: '',
                        tzShort: '',
                        init() {
                            this.update();
                            setInterval(() => this.update(), 1000);
                        },
                        update() {
                            const now = new Date();
                            this.time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
                            this.date = now.toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
                            try {
                                const resolvedTz = Intl.DateTimeFormat().resolvedOptions().timeZone || '{{ $currentTimezone }}';
                                this.tz = resolvedTz;
                                const parts = new Intl.DateTimeFormat([], { timeZoneName: 'short' }).formatToParts(now);
                                const tzPart = parts.find(p => p.type === 'timeZoneName');
                                this.tzShort = tzPart ? tzPart.value : resolvedTz.split('/').pop().replace('_', ' ');
                            } catch(e) {
                                this.tz = '{{ $currentTimezone }}';
                                this.tzShort = 'UTC';
                            }
                        }
                    }"
                    class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-white/95 via-indigo-50/50 to-white/95 dark:from-zinc-900/95 dark:via-indigo-950/90 dark:to-zinc-900/95 border border-indigo-200/90 dark:border-indigo-400/35 p-4 shadow-lg shadow-indigo-100/50 dark:shadow-black/50 backdrop-blur-md min-w-[280px] sm:min-w-[310px] transition-all hover:border-indigo-300 dark:hover:border-indigo-400/60 hover-lift"
                >
                    <!-- Glowing back-lights -->
                    <div class="absolute -top-10 -right-10 size-28 bg-indigo-500/10 dark:bg-indigo-500/25 rounded-full blur-2xl pointer-events-none group-hover:bg-indigo-500/20 dark:group-hover:bg-indigo-500/35 transition-all"></div>
                    <div class="absolute -bottom-10 -left-10 size-28 bg-violet-500/10 dark:bg-violet-500/25 rounded-full blur-2xl pointer-events-none"></div>

                    <!-- Header Row: Live indicator & Timezone Pill -->
                    <div class="relative z-10 flex items-center justify-between pb-2 border-b border-indigo-100 dark:border-indigo-500/20">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">
                            <span class="size-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-glow-pulse"></span>
                            <span class="tracking-wide">LIVE SYSTEM CLOCK</span>
                        </div>
                        <div class="inline-flex items-center gap-1.5 text-[11px] font-medium text-indigo-700 dark:text-indigo-200 bg-indigo-50 dark:bg-indigo-500/20 px-2.5 py-0.5 rounded-lg border border-indigo-100 dark:border-indigo-500/30">
                            <svg class="size-3 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="tzShort || '{{ $currentTimezone }}'">{{ $currentTimezone }}</span>
                        </div>
                    </div>

                    <!-- Center: Large Digital Clock -->
                    <div class="relative z-10 py-2.5 flex items-baseline justify-between">
                        <div class="flex items-center gap-2">
                            <span 
                                class="font-mono text-3xl sm:text-4xl font-black tracking-tight text-slate-900 dark:text-white tabular-nums drop-shadow-[0_2px_8px_rgba(99,102,241,0.2)] dark:drop-shadow-[0_2px_12px_rgba(99,102,241,0.5)]" 
                                x-text="time"
                            >
                                {{ $currentTime }}
                            </span>
                        </div>
                    </div>

                    <!-- Bottom: Full Formatted Date & Location -->
                    <div class="relative z-10 flex items-center justify-between text-[11px] text-slate-500 dark:text-zinc-300 pt-2 border-t border-indigo-100 dark:border-indigo-500/20">
                        <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-zinc-200">
                            <svg class="size-3.5 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span x-text="date">{{ $currentDate }}</span>
                        </div>
                        <span class="text-[10px] text-indigo-600/80 dark:text-indigo-300/80 font-mono" x-text="tz ? tz.split('/').pop().replace('_', ' ') : 'Synchronized'">Synchronized</span>
                    </div>
                </div>

                <!-- Action Controls: Space filter, AI buttons, CSV export -->
                <div class="flex flex-wrap items-center gap-2 w-full justify-start sm:justify-end">
                    <!-- Space Filter -->
                    <div class="relative flex-1 sm:flex-none">
                        <select 
                            wire:model.live="selectedSpaceId" 
                            class="w-full text-xs font-semibold rounded-xl border border-indigo-200 dark:border-indigo-500/30 bg-white hover:bg-slate-50 dark:bg-zinc-800/90 dark:hover:bg-zinc-800 text-slate-800 dark:text-white py-2 px-3 shadow-2xs focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-hidden backdrop-blur-xs cursor-pointer hover-lift-sm"
                        >
                            <option value="" class="bg-white text-slate-800 dark:bg-zinc-900 dark:text-white">All Spaces Scope</option>
                            @foreach($spaces as $sp)
                                <option value="{{ $sp->id }}" class="bg-white text-slate-800 dark:bg-zinc-900 dark:text-white">{{ $sp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- AI Standup Button -->
                    <button 
                        wire:click="generateStandup" 
                        type="button" 
                        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-violet-50 hover:bg-violet-100 text-violet-700 border border-violet-200 dark:bg-violet-500/20 dark:hover:bg-violet-500/30 dark:text-violet-200 dark:border-violet-400/40 transition-all cursor-pointer shadow-2xs hover-lift-sm active:scale-95"
                        title="Generate personalized daily standup with Google Gemini"
                    >
                        <span wire:loading.remove wire:target="generateStandup">
                            <svg class="size-3.5 text-violet-600 dark:text-violet-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </span>
                        <span wire:loading wire:target="generateStandup" class="animate-spin size-3.5 border-2 border-violet-500 dark:border-violet-400 border-t-transparent rounded-full"></span>
                        <span>My Standup</span>
                    </button>

                    <!-- AI Executive Brief Button -->
                    <button 
                        wire:click="generateTeamAiSummary" 
                        type="button" 
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white shadow-md shadow-indigo-600/30 transition-all cursor-pointer hover-lift-sm active:scale-95 border border-indigo-400/30"
                        title="Generate executive team briefing with Google Gemini"
                    >
                        <span wire:loading.remove wire:target="generateTeamAiSummary">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </span>
                        <span wire:loading wire:target="generateTeamAiSummary" class="animate-spin size-3.5 border-2 border-white border-t-transparent rounded-full"></span>
                        <span>AI Executive Brief</span>
                    </button>

                    <!-- CSV Export Buttons -->
                    <div class="flex items-center gap-1.5">
                        <button 
                            wire:click="exportTasksCsv" 
                            type="button" 
                            class="p-2 rounded-xl text-xs font-medium bg-white hover:bg-slate-50 dark:bg-zinc-800/90 dark:hover:bg-zinc-700 text-slate-600 hover:text-slate-900 dark:text-zinc-300 dark:hover:text-white border border-indigo-100 dark:border-indigo-500/20 transition-colors shadow-2xs hover-lift-sm"
                            title="Export Tasks CSV"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </button>

                        <button 
                            wire:click="exportTicketsCsv" 
                            type="button" 
                            class="p-2 rounded-xl text-xs font-medium bg-white hover:bg-slate-50 dark:bg-zinc-800/90 dark:hover:bg-zinc-700 text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-white border border-indigo-100 dark:border-indigo-500/20 transition-colors shadow-2xs hover-lift-sm"
                            title="Export Tickets CSV"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Focus Mode Switcher Tabs -->
        <div class="mt-6 pt-5 border-t border-indigo-100 dark:border-indigo-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="inline-flex items-center p-1 rounded-2xl bg-slate-100/90 dark:bg-zinc-900/90 border border-slate-200/80 dark:border-indigo-500/20 text-xs shadow-inner">
                <button 
                    wire:click="setTab('overview')"
                    class="px-4 py-2 rounded-xl font-bold transition-all cursor-pointer {{ $activeTab === 'overview' ? 'bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 hover:text-slate-900 dark:text-zinc-300 dark:hover:text-white' }}"
                >
                    Unified Overview
                </button>
                <button 
                    wire:click="setTab('tasks')"
                    class="px-4 py-2 rounded-xl font-bold transition-all cursor-pointer {{ $activeTab === 'tasks' ? 'bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 hover:text-slate-900 dark:text-zinc-300 dark:hover:text-white' }}"
                >
                    Tasks &amp; Delivery
                </button>
                <button 
                    wire:click="setTab('tickets')"
                    class="px-4 py-2 rounded-xl font-bold transition-all cursor-pointer {{ $activeTab === 'tickets' ? 'bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 hover:text-slate-900 dark:text-zinc-300 dark:hover:text-white' }}"
                >
                    Support &amp; Helpdesk
                </button>
            </div>

            <!-- Direct Quick Links -->
            <div class="flex items-center gap-3 text-xs">
                <a href="{{ route('workspace.tasks', ['workspace' => $workspace->slug]) }}" class="font-semibold text-slate-600 hover:text-indigo-600 dark:text-zinc-300 dark:hover:text-white transition-colors" wire:navigate>
                    Task Board &rarr;
                </a>
                <span class="text-indigo-300 dark:text-indigo-400/40">•</span>
                <a href="{{ route('workspace.tickets.queue', ['workspace' => $workspace->slug]) }}" class="font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-white transition-colors" wire:navigate>
                    Triage Queue &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- AI Briefing Cards (if active) -->
    @if($teamAiSummary)
        <div class="p-6 sm:p-7 rounded-3xl border border-indigo-400/40 dark:border-indigo-800/80 bg-gradient-to-r from-indigo-500/10 via-purple-500/5 to-white dark:from-indigo-950/60 dark:via-purple-950/30 dark:to-slate-900 shadow-lg shadow-indigo-950/10 relative animate-in fade-in zoom-in-98 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-indigo-200/60 dark:border-indigo-800/60 mb-4">
                <div class="flex items-center gap-2.5 font-bold text-sm text-indigo-950 dark:text-indigo-200">
                    <span class="p-2 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/30">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </span>
                    <span class="text-base tracking-tight">Gemini AI Executive Digest</span>
                </div>
                <button wire:click="$set('teamAiSummary', null)" class="text-xs px-3 py-1.5 rounded-xl bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-200 font-semibold cursor-pointer transition-colors">
                    &times; Dismiss
                </button>
            </div>
            <div class="text-xs text-slate-800 dark:text-slate-200 prose dark:prose-invert max-w-none leading-relaxed">
                {!! Str::markdown($teamAiSummary) !!}
            </div>
        </div>
    @endif

    @if($myStandupReport)
        <div class="p-6 sm:p-7 rounded-3xl border border-purple-400/40 dark:border-purple-800/80 bg-gradient-to-r from-purple-500/10 via-indigo-500/5 to-white dark:from-purple-950/60 dark:via-indigo-950/30 dark:to-slate-900 shadow-lg shadow-purple-950/10 relative animate-in fade-in zoom-in-98 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-purple-200/60 dark:border-purple-800/60 mb-4">
                <div class="flex items-center gap-2.5 font-bold text-sm text-purple-950 dark:text-purple-200">
                    <span class="p-2 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-600/30">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </span>
                    <span class="text-base tracking-tight">Gemini Daily Standup Briefing</span>
                </div>
                <button wire:click="$set('myStandupReport', null)" class="text-xs px-3 py-1.5 rounded-xl bg-purple-100 hover:bg-purple-200 dark:bg-purple-900/60 text-purple-700 dark:text-purple-200 font-semibold cursor-pointer transition-colors">
                    &times; Dismiss
                </button>
            </div>
            <div class="text-xs text-slate-800 dark:text-slate-200 prose dark:prose-invert max-w-none leading-relaxed">
                {!! Str::markdown($myStandupReport) !!}
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- INTERACTIVE PROJECT INFLUX & COMPARATIVE ANALYTICS HUB                     -->
    <!-- ========================================================================= -->
    <div class="space-y-6">
        
        <!-- Interactive Time-Travel & Analytics Toolbar -->
        <div class="rounded-3xl bg-gradient-to-br from-white via-slate-50/90 to-indigo-50/20 dark:from-zinc-900 dark:via-zinc-900/95 dark:to-indigo-950/20 border border-slate-200/90 dark:border-zinc-800 shadow-xs p-5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <!-- Left Title & Period Indicator -->
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 shadow-xs">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </span>
                        <h2 class="text-base font-black text-slate-900 dark:text-white tracking-tight">
                            Interactive Influx &amp; Workload Analytics
                        </h2>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 flex flex-wrap items-center gap-1.5">
                        <span>Comparing</span>
                        <strong class="text-indigo-600 dark:text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-lg border border-indigo-500/20 font-bold">
                            {{ $timeBoundaries['label'] }}
                        </strong>
                        <span>vs previous period</span>
                        <strong class="text-slate-700 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-zinc-700 font-semibold">
                            {{ $timeBoundaries['prev_label'] }}
                        </strong>
                    </p>
                </div>

                <!-- Right Controls: Filter Pills, Selectors, View Toggles -->
                <div class="flex flex-wrap items-center gap-2.5">
                    
                    <!-- Time Granularity Switcher Pills -->
                    <div class="inline-flex items-center p-1 rounded-2xl bg-slate-100 dark:bg-zinc-800/80 border border-slate-200/80 dark:border-zinc-700/80 text-xs shadow-inner">
                        <button 
                            wire:click="setTimeRange('month')"
                            class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $timeRange === 'month' ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white' }}"
                        >
                            Month-Wise
                        </button>
                        <button 
                            wire:click="setTimeRange('last_month')"
                            class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $timeRange === 'last_month' ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white' }}"
                        >
                            Last Month
                        </button>
                        <button 
                            wire:click="setTimeRange('year')"
                            class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $timeRange === 'year' ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white' }}"
                        >
                            Year-Wise
                        </button>
                        <button 
                            wire:click="setTimeRange('all')"
                            class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $timeRange === 'all' ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white' }}"
                        >
                            12 Months
                        </button>
                    </div>

                    <!-- Year Selector -->
                    <select 
                        wire:model.live="selectedYear"
                        class="text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 py-1.5 px-2.5 shadow-xs focus:ring-1 focus:ring-indigo-500 cursor-pointer hover-lift-sm"
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
                            class="text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 py-1.5 px-2.5 shadow-xs focus:ring-1 focus:ring-indigo-500 cursor-pointer hover-lift-sm"
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
                        class="text-xs font-semibold rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 py-1.5 px-2.5 shadow-xs focus:ring-1 focus:ring-indigo-500 max-w-[170px] truncate cursor-pointer hover-lift-sm"
                        title="Filter by Project"
                    >
                        <option value="">All Projects Scope</option>
                        @foreach($workspaceProjects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>

                    <!-- Chart Style Toggle (Line vs Bar) -->
                    <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 dark:bg-zinc-800/80 border border-slate-200/80 dark:border-zinc-700/80 text-xs">
                        <button 
                            wire:click="setChartType('line')"
                            class="p-1.5 rounded-lg transition-colors cursor-pointer {{ $chartType === 'line' ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-xs' : 'text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200' }}"
                            title="Line Curve View"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </button>
                        <button 
                            wire:click="setChartType('bar')"
                            class="p-1.5 rounded-lg transition-colors cursor-pointer {{ $chartType === 'bar' ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-xs' : 'text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200' }}"
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
            
            <!-- 1. Tickets Influx Delta (Rich Indigo/Iris) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-indigo-500/[0.08] via-white to-violet-50/30 dark:from-indigo-950/40 dark:via-zinc-900 dark:to-zinc-900 border border-indigo-200/80 dark:border-indigo-800/60 shadow-xs hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-lg transition-all flex flex-col justify-between space-y-3 hover-lift">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">Incoming Ticket Influx</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $ticketCreationDelta['current'] }}</span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-bold px-2 py-0.5 rounded-lg {{ $ticketCreationDelta['pct'] > 0 ? 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30' : ($ticketCreationDelta['pct'] < 0 ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-slate-100 dark:bg-zinc-800 text-slate-500') }}">
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
                    <div class="size-11 rounded-2xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/30 hover:scale-105 transition-transform">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-zinc-400 pt-2 border-t border-indigo-100/60 dark:border-zinc-800">
                    <span>Prior period: <strong class="text-slate-800 dark:text-zinc-200">{{ $ticketCreationDelta['previous'] }}</strong></span>
                    <span>Net: <strong class="{{ $ticketCreationDelta['delta'] >= 0 ? 'text-amber-500 font-bold' : 'text-emerald-500 font-bold' }}">{{ $ticketCreationDelta['delta'] > 0 ? '+' : '' }}{{ $ticketCreationDelta['delta'] }}</strong></span>
                </div>
            </div>

            <!-- 2. Tickets Resolved Delta (Rich Emerald/Jade) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-emerald-500/[0.08] via-white to-teal-50/30 dark:from-emerald-950/40 dark:via-zinc-900 dark:to-zinc-900 border border-emerald-200/80 dark:border-emerald-800/60 shadow-xs hover:border-emerald-400 dark:hover:border-emerald-500 hover:shadow-lg transition-all flex flex-col justify-between space-y-3 hover-lift">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">Tickets Resolved</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $ticketResolutionDelta['current'] }}</span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-bold px-2 py-0.5 rounded-lg {{ $ticketResolutionDelta['pct'] >= 0 ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30' }}">
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
                    <div class="size-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/30 hover:scale-105 transition-transform">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-zinc-400 pt-2 border-t border-emerald-100/60 dark:border-zinc-800">
                    <span>Prior period: <strong class="text-slate-800 dark:text-zinc-200">{{ $ticketResolutionDelta['previous'] }}</strong></span>
                    <span>Net: <strong class="{{ $ticketResolutionDelta['delta'] >= 0 ? 'text-emerald-500 font-bold' : 'text-rose-500 font-bold' }}">{{ $ticketResolutionDelta['delta'] > 0 ? '+' : '' }}{{ $ticketResolutionDelta['delta'] }}</strong></span>
                </div>
            </div>

            <!-- 3. Tasks Created Delta (Rich Electric Iris/Sky) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-sky-500/[0.08] via-white to-indigo-50/30 dark:from-sky-950/40 dark:via-zinc-900 dark:to-zinc-900 border border-sky-200/80 dark:border-sky-800/60 shadow-xs hover:border-sky-400 dark:hover:border-sky-500 hover:shadow-lg transition-all flex flex-col justify-between space-y-3 hover-lift">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">Tasks Created</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $taskCreationDelta['current'] }}</span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-bold px-2 py-0.5 rounded-lg {{ $taskCreationDelta['pct'] >= 0 ? 'bg-sky-500/15 text-sky-600 dark:text-sky-400 border border-sky-500/30' : 'bg-slate-100 dark:bg-zinc-800 text-slate-500' }}">
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
                    <div class="size-11 rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-sky-500/30 hover:scale-105 transition-transform">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-zinc-400 pt-2 border-t border-sky-100/60 dark:border-zinc-800">
                    <span>Prior period: <strong class="text-slate-800 dark:text-zinc-200">{{ $taskCreationDelta['previous'] }}</strong></span>
                    <span>Net: <strong class="text-sky-500 font-bold">{{ $taskCreationDelta['delta'] > 0 ? '+' : '' }}{{ $taskCreationDelta['delta'] }}</strong></span>
                </div>
            </div>

            <!-- 4. Tasks Completed Delta (Rich Mint/Iris) -->
            <div class="p-5 rounded-3xl bg-gradient-to-br from-teal-500/[0.08] via-white to-emerald-50/30 dark:from-teal-950/40 dark:via-zinc-900 dark:to-zinc-900 border border-teal-200/80 dark:border-teal-800/60 shadow-xs hover:border-teal-400 dark:hover:border-teal-500 hover:shadow-lg transition-all flex flex-col justify-between space-y-3 hover-lift">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">Tasks Completed</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $taskCompletionDelta['current'] }}</span>
                            <span class="inline-flex items-center gap-0.5 text-[11px] font-bold px-2 py-0.5 rounded-lg {{ $taskCompletionDelta['pct'] >= 0 ? 'bg-teal-500/15 text-teal-600 dark:text-teal-400 border border-teal-500/30' : 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30' }}">
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
                    <div class="size-11 rounded-2xl bg-gradient-to-br from-teal-500 via-teal-600 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-teal-500/30 hover:scale-105 transition-transform">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-zinc-400 pt-2 border-t border-teal-100/60 dark:border-zinc-800">
                    <span>Prior period: <strong class="text-slate-800 dark:text-zinc-200">{{ $taskCompletionDelta['previous'] }}</strong></span>
                    <span>Net: <strong class="{{ $taskCompletionDelta['delta'] >= 0 ? 'text-teal-500 font-bold' : 'text-rose-500 font-bold' }}">{{ $taskCompletionDelta['delta'] > 0 ? '+' : '' }}{{ $taskCompletionDelta['delta'] }}</strong></span>
                </div>
            </div>

        </div>

        <!-- Interactive Visual Analytics Charts (Playable Canvases) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Chart 1 (2 Columns): Velocity Curve: Tickets vs Tasks Trend -->
            <div class="lg:col-span-2 p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all hover-lift flex flex-col justify-between space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="size-2.5 rounded-full bg-indigo-500 animate-pulse shadow-[0_0_8px_rgba(99,102,241,0.6)]"></span>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Tickets vs Tasks Inflow &amp; Velocity Trend</h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                            Click any legend item to toggle datasets. Hover over points for exact counts.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-[11px] text-slate-400">
                        <span class="inline-flex items-center gap-1 font-medium">
                            <span class="size-2 rounded-full bg-indigo-500"></span> Influx
                        </span>
                        <span class="inline-flex items-center gap-1 font-medium">
                            <span class="size-2 rounded-full bg-emerald-500"></span> Resolved
                        </span>
                        <span class="inline-flex items-center gap-1 font-medium">
                            <span class="size-2 rounded-full bg-sky-400"></span> Tasks
                        </span>
                    </div>
                </div>

                <!-- Canvas Wrapper -->
                <div 
                    class="h-72 w-full relative"
                    x-data="velocityTrendChart()"
                    wire:key="velocity-trend-canvas-{{ $timeRange }}-{{ $selectedYear }}-{{ $selectedMonth }}-{{ $filterProjectId }}-{{ $chartType }}"
                >
                    <canvas x-ref="canvas"></canvas>
                    <script type="application/json" x-ref="chartData">
                        {!! json_encode([
                            'labels' => $chartLabels,
                            'ticketsCreated' => $chartTicketsCreated,
                            'ticketsResolved' => $chartTicketsResolved,
                            'tasksCreated' => $chartTasksCreated,
                            'tasksCompleted' => $chartTasksCompleted,
                            'chartType' => $chartType,
                        ]) !!}
                    </script>
                </div>
            </div>

            <!-- Chart 2: Issue Category Breakdown Doughnut -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all hover-lift flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="size-2.5 rounded-full bg-violet-500 shadow-[0_0_8px_rgba(139,92,246,0.6)]"></span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Tickets by Category Breakdown</h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                        Categorical distribution for {{ $timeBoundaries['label'] }}.
                    </p>
                </div>

                <div 
                    class="h-72 w-full relative flex items-center justify-center"
                    x-data="categoryDoughnutChart()"
                    wire:key="category-breakdown-canvas-{{ $timeRange }}-{{ $selectedYear }}-{{ $selectedMonth }}-{{ $filterProjectId }}"
                >
                    <canvas x-ref="canvas"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pb-7">
                        <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ array_sum($chartCategoryData) }}
                        </span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                            Tickets
                        </span>
                    </div>
                    <script type="application/json" x-ref="chartData">
                        {!! json_encode([
                            'labels' => $chartCategoryLabels,
                            'data' => $chartCategoryData,
                        ]) !!}
                    </script>
                </div>
            </div>

        </div>

        <!-- Chart 3: Project Workload & Influx Matrix (Horizontal Bar Comparison) -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all hover-lift space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="size-2.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]"></span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Project Workload vs Ticket Influx Comparison</h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                        Highlights which project is carrying high task backlogs vs incoming ticket volume.
                    </p>
                </div>
                <span class="text-xs font-semibold text-slate-400">
                    {{ count($chartProjectNames) }} Projects Tracked
                </span>
            </div>

            <div 
                class="h-64 w-full relative"
                x-data="projectWorkloadChart()"
                wire:key="project-matrix-bar-canvas-{{ $timeRange }}-{{ $selectedYear }}-{{ $selectedMonth }}-{{ $filterProjectId }}"
            >
                <canvas x-ref="canvas"></canvas>
                <script type="application/json" x-ref="chartData">
                    {!! json_encode([
                        'labels' => $chartProjectNames,
                        'tasks' => $chartProjectTasks,
                        'tickets' => $chartProjectTickets,
                    ]) !!}
                </script>
            </div>
        </div>

        <!-- Project Deep-Dive Matrix & Issue Category MoM Comparison Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Project Deep-Dive Matrix Table (2 Columns) -->
            <div class="lg:col-span-2 p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all hover-lift space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Project Workload &amp; Influx Breakdown</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Month-over-month influx comparisons per project.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-zinc-800 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                <th class="pb-3 font-bold">Project</th>
                                <th class="pb-3 font-bold text-center">Tasks (Done/Total)</th>
                                <th class="pb-3 font-bold text-center">Tickets ({{ $timeBoundaries['label'] }})</th>
                                <th class="pb-3 font-bold text-center">Tickets ({{ $timeBoundaries['prev_label'] }})</th>
                                <th class="pb-3 font-bold text-center">MoM Trend</th>
                                <th class="pb-3 font-bold text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/80">
                            @forelse($projectsMatrix as $row)
                                <tr class="hover:bg-indigo-50/40 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="size-2.5 rounded-full" style="background-color: {{ $row['space_color'] }}"></span>
                                            <div>
                                                <span class="font-bold text-slate-900 dark:text-white block">{{ $row['project_name'] }}</span>
                                                <span class="text-[10px] text-slate-400">{{ $row['space_name'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $row['completed_tasks'] }}/{{ $row['total_tasks'] }}</span>
                                        <div class="h-1.5 w-16 mx-auto bg-slate-100 dark:bg-zinc-800 rounded-full mt-1 overflow-hidden">
                                            <div 
                                                class="h-full bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 rounded-full"
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
                                        <span class="font-medium text-slate-500">
                                            {{ $row['tickets_count_previous'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="text-[11px] font-bold {{ $row['ticket_delta'] > 0 ? 'text-amber-500' : ($row['ticket_delta'] < 0 ? 'text-emerald-500' : 'text-slate-400') }}">
                                            {{ $row['ticket_delta'] > 0 ? '+' : '' }}{{ $row['ticket_delta'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        @if($row['open_tickets'] > 3)
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                                                High Attention
                                            </span>
                                        @elseif($row['tickets_count_current'] > 0)
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                                Active Influx
                                            </span>
                                        @else
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                                Stable
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-slate-400">No projects found for current filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Issue Category MoM Comparison Matrix Table (1 Column) -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all hover-lift space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Issue Category MoM Comparison</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">Changes in issue types vs previous period.</p>
                </div>

                <div class="space-y-3">
                    @forelse($categoriesMatrix as $cat)
                        <div class="p-3.5 rounded-2xl bg-gradient-to-br from-slate-50 to-indigo-50/20 dark:from-zinc-800/60 dark:to-zinc-900/60 border border-slate-200/70 dark:border-zinc-800/80 space-y-1.5 hover-lift-sm transition-all">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $cat['name'] }}</span>
                                <span class="text-[11px] font-bold {{ $cat['delta'] > 0 ? 'text-amber-500' : ($cat['delta'] < 0 ? 'text-emerald-500' : 'text-slate-400') }}">
                                    {{ $cat['delta'] > 0 ? '+' : '' }}{{ $cat['delta'] }} ({{ $cat['pct'] > 0 ? '+' : '' }}{{ $cat['pct'] }}%)
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400">
                                <span>This period: <strong class="text-indigo-600 dark:text-indigo-400">{{ $cat['current'] }}</strong></span>
                                <span>Prior: <strong class="text-slate-600 dark:text-slate-300">{{ $cat['previous'] }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 text-center py-6">No ticket categories logged.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Gemini AI Trend Diagnostics & Interactive Query Assistant -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-br from-indigo-500/[0.08] via-violet-500/[0.04] to-transparent dark:from-indigo-950/40 dark:via-zinc-900 dark:to-zinc-900 border border-indigo-200/80 dark:border-indigo-500/30 shadow-xs hover:shadow-lg transition-all space-y-4 backdrop-blur-xs relative overflow-hidden hover-lift">
            <div class="absolute -top-16 -right-16 size-48 bg-indigo-500/10 dark:bg-violet-500/15 rounded-full blur-2xl pointer-events-none animate-float-slow"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-2xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-500/30 flex items-center justify-center">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Gemini AI Analytics Diagnostics</span>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30">AI Powered</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                            Ask questions or generate automated trend diagnosis across projects, ticket spikes, and velocity.
                        </p>
                    </div>
                </div>

                <button 
                    wire:click="generateAiAnalyticsInsight"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 hover:from-indigo-600 hover:to-violet-700 text-white shadow-md shadow-indigo-600/30 transition-all cursor-pointer shrink-0 disabled:opacity-50 active:scale-95 border border-white/20"
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
            <form wire:submit="generateAiAnalyticsInsight" class="relative z-10 flex gap-2">
                <input 
                    type="text" 
                    wire:model="aiPromptQuery" 
                    placeholder="Ask Gemini anything about this data (e.g. 'Why did tickets spike?', 'Which project has the highest risk?')..."
                    class="flex-1 text-xs px-4 py-2.5 rounded-xl bg-white dark:bg-zinc-900/90 border border-indigo-200 dark:border-indigo-500/40 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-zinc-500 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 shadow-inner"
                />
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/70 dark:hover:bg-indigo-900/70 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60 transition-colors cursor-pointer"
                >
                    Ask
                </button>
            </form>

            <!-- Rendered AI Insight -->
            @if($aiAnalyticsInsight)
                <div class="relative z-10 p-5 rounded-2xl bg-white/95 dark:bg-zinc-900/95 border border-indigo-200 dark:border-indigo-500/40 text-xs text-slate-800 dark:text-zinc-200 leading-relaxed space-y-2 animate-in fade-in zoom-in-98 duration-200 shadow-sm">
                    <div class="flex items-center justify-between pb-2 border-b border-indigo-100 dark:border-indigo-500/20">
                        <span class="font-bold text-indigo-600 dark:text-indigo-300">Gemini Trend Insights</span>
                        <button wire:click="$set('aiAnalyticsInsight', null)" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs cursor-pointer">
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

    <!-- 1. HERO EXECUTIVE KPI CARDS (4 Hero Cards with Rich Colors) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Delivery Velocity & Completion (Rich Emerald) -->
        <div class="p-5 rounded-3xl bg-gradient-to-br from-emerald-500/[0.08] via-white to-teal-50/30 dark:from-emerald-950/40 dark:via-zinc-900 dark:to-zinc-900 border border-emerald-200/80 dark:border-emerald-800/60 shadow-xs hover:border-emerald-400 dark:hover:border-emerald-500 hover:shadow-lg transition-all flex flex-col justify-between space-y-4 hover-lift">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">Sprint Delivery Velocity</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $completionRate }}%</span>
                        <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/15 border border-emerald-500/30 px-2 py-0.5 rounded-lg">
                            {{ $completionRate >= 50 ? 'On Track' : 'In Flight' }}
                        </span>
                    </div>
                </div>
                <div class="size-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/30 hover:scale-105 transition-transform">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="h-2.5 w-full bg-slate-100 dark:bg-zinc-800 rounded-full overflow-hidden p-0.5 border border-emerald-500/10">
                    <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400 rounded-full transition-all duration-700 shadow-xs" style="width: {{ $completionRate }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] text-slate-500 dark:text-zinc-400">
                    <span>{{ $completedTasks }} of {{ $totalTasks }} tasks shipped</span>
                    @if($totalBlocked > 0)
                        <span class="text-rose-500 font-bold">{{ $totalBlocked }} Blocked</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Personal Focus & My Open Tasks (Rich Iris/Indigo) -->
        <div class="p-5 rounded-3xl bg-gradient-to-br from-indigo-500/[0.08] via-white to-violet-50/30 dark:from-indigo-950/40 dark:via-zinc-900 dark:to-zinc-900 border border-indigo-200/80 dark:border-indigo-800/60 shadow-xs hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-lg transition-all flex flex-col justify-between space-y-4 hover-lift">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">My Pending Tasks</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $myOpen }}</span>
                        @if($myOverdue > 0)
                            <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400 bg-rose-500/15 border border-rose-500/30 px-2 py-0.5 rounded-lg">
                                {{ $myOverdue }} Overdue
                            </span>
                        @else
                            <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-500/15 border border-indigo-500/30 px-2 py-0.5 rounded-lg">
                                Active Focus
                            </span>
                        @endif
                    </div>
                </div>
                <div class="size-11 rounded-2xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/30 hover:scale-105 transition-transform">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-zinc-400 pt-1 border-t border-indigo-100/60 dark:border-zinc-800">
                <span>Today: <strong class="text-amber-500 font-bold">{{ $myDueToday }}</strong></span>
                <span>•</span>
                <span>This Week: <strong class="text-slate-800 dark:text-zinc-200 font-bold">{{ $myDueThisWeek }}</strong></span>
                <span>•</span>
                <span>Tickets: <strong class="text-indigo-500 font-bold">{{ $ticketsAssignedToMe }}</strong></span>
            </div>
        </div>

        <!-- 3. Support Helpdesk & SLA Health (Rich Royal Violet/Iris) -->
        <div class="p-5 rounded-3xl bg-gradient-to-br from-violet-500/[0.08] via-white to-indigo-50/30 dark:from-violet-950/40 dark:via-zinc-900 dark:to-zinc-900 border border-violet-200/80 dark:border-violet-800/60 shadow-xs hover:border-violet-400 dark:hover:border-violet-500 hover:shadow-lg transition-all flex flex-col justify-between space-y-4 hover-lift">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">Support &amp; SLA Compliance</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-black text-violet-600 dark:text-violet-400">{{ $slaComplianceRate }}%</span>
                        <span class="text-[11px] font-bold {{ $ticketsOverdue > 0 ? 'text-rose-600 dark:text-rose-400 bg-rose-500/15 border border-rose-500/30' : 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/15 border border-emerald-500/30' }} px-2 py-0.5 rounded-lg">
                            {{ $ticketsOverdue > 0 ? $ticketsOverdue . ' Breached' : '100% Target' }}
                        </span>
                    </div>
                </div>
                <div class="size-11 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-violet-500/30 hover:scale-105 transition-transform">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-zinc-400 pt-1 border-t border-violet-100/60 dark:border-zinc-800">
                <span>In Triage: <strong class="text-slate-900 dark:text-white font-bold">{{ $ticketsOpen }}</strong></span>
                <span>•</span>
                <span>In Progress: <strong class="text-slate-900 dark:text-white font-bold">{{ $ticketsInProgress }}</strong></span>
                <span>•</span>
                <span>Avg: <strong class="text-emerald-500 font-bold">{{ $avgResolutionTime }}</strong></span>
            </div>
        </div>

        <!-- 4. Operational Risk & Attention Radar (Rich Alert/Protective Shield) -->
        <div class="p-5 rounded-3xl bg-gradient-to-br {{ $totalAttentionItems > 0 ? 'from-amber-500/[0.1] via-white to-rose-50/30 dark:from-amber-950/40 dark:via-zinc-900 dark:to-zinc-900 border-amber-300 dark:border-amber-700/60' : 'from-emerald-500/[0.08] via-white to-emerald-50/30 dark:from-emerald-950/40 dark:via-zinc-900 dark:to-zinc-900 border-emerald-200/80 dark:border-emerald-800/60' }} border shadow-xs hover:shadow-lg transition-all flex flex-col justify-between space-y-4 hover-lift">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">Platform Risk Radar</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-black {{ $totalAttentionItems > 0 ? 'text-amber-500' : 'text-emerald-500' }}">
                            {{ $totalAttentionItems }}
                        </span>
                        <span class="text-[11px] font-bold {{ $totalAttentionItems > 0 ? 'text-amber-600 dark:text-amber-400 bg-amber-500/15 border border-amber-500/30' : 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/15 border border-emerald-500/30' }} px-2 py-0.5 rounded-lg">
                            {{ $totalAttentionItems > 0 ? 'Action Required' : 'All Clear' }}
                        </span>
                    </div>
                </div>
                <div class="size-11 rounded-2xl {{ $totalAttentionItems > 0 ? 'bg-gradient-to-br from-amber-500 to-rose-600 text-white shadow-md shadow-amber-500/30' : 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/30' }} flex items-center justify-center hover:scale-105 transition-transform">
                    @if($totalAttentionItems > 0)
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    @else
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-zinc-400 pt-1 border-t border-slate-100 dark:border-zinc-800">
                <span>Overdue Tasks: <strong class="{{ $totalOverdue > 0 ? 'text-rose-500 font-bold' : '' }}">{{ $totalOverdue }}</strong></span>
                <span>•</span>
                <span>Breached SLAs: <strong class="{{ $ticketsOverdue > 0 ? 'text-rose-500 font-bold' : '' }}">{{ $ticketsOverdue }}</strong></span>
                <span>•</span>
                <span>Blocked: <strong class="{{ $totalBlocked > 0 ? 'text-rose-500 font-bold' : '' }}">{{ $totalBlocked }}</strong></span>
            </div>
        </div>
    </div>

    <!-- 2. URGENT ATTENTION CENTER (When items require action) -->
    @if($criticalTasks->isNotEmpty() || $urgentTickets->isNotEmpty())
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-rose-300 dark:hover:border-zinc-700 transition-all hover-lift space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="relative flex size-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full size-3 bg-rose-500"></span>
                    </span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Needs Immediate Attention</h3>
                </div>
                <span class="text-xs text-slate-400">Click any item to view or resolve immediately</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Critical Tasks -->
                <div class="space-y-2">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                        <span>Critical Tasks ({{ $criticalTasks->count() }})</span>
                        <a href="{{ route('workspace.tasks', ['workspace' => $workspace->slug]) }}" class="hover:underline text-indigo-500 font-semibold" wire:navigate>View Board &rarr;</a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-zinc-800/80 rounded-2xl border border-slate-200/70 dark:border-zinc-800 bg-slate-50/60 dark:bg-zinc-900/80 px-3.5">
                        @forelse($criticalTasks as $task)
                            <div 
                                wire:click="$dispatch('open-task-detail', { taskId: {{ $task->id }} })"
                                class="py-2.5 flex items-center justify-between gap-3 cursor-pointer hover:bg-slate-100/80 dark:hover:bg-zinc-800/80 -mx-1 px-2.5 rounded-xl transition-all group hover-lift-sm"
                            >
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-slate-900 dark:text-white group-hover:text-indigo-500 truncate">{{ $task->title }}</p>
                                    <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                                        <span>{{ $task->taskList?->project?->name }}</span>
                                        <span>•</span>
                                        <span class="{{ $task->isOverdue() ? 'text-rose-500 font-bold' : '' }}">
                                            {{ $task->due_date ? 'Due ' . $task->due_date->format('M j') : 'No due date' }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-lg font-bold {{ $task->isOverdue() ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30' }}">
                                    {{ $task->status?->name }}
                                </span>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-slate-400">Zero critical task blockers.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Urgent Tickets -->
                <div class="space-y-2">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                        <span>Urgent / Breached Tickets ({{ $urgentTickets->count() }})</span>
                        <a href="{{ route('workspace.tickets.queue', ['workspace' => $workspace->slug]) }}" class="hover:underline text-indigo-500 font-semibold" wire:navigate>Triage Queue &rarr;</a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-zinc-800/80 rounded-2xl border border-slate-200/70 dark:border-zinc-800 bg-slate-50/60 dark:bg-zinc-900/80 px-3.5">
                        @forelse($urgentTickets as $tick)
                            <div 
                                wire:click="$dispatch('open-ticket-detail', { ticketId: {{ $tick->id }} })"
                                class="py-2.5 flex items-center justify-between gap-3 cursor-pointer hover:bg-slate-100/80 dark:hover:bg-zinc-800/80 -mx-1 px-2.5 rounded-xl transition-all group hover-lift-sm"
                            >
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-mono font-bold text-indigo-500 dark:text-indigo-400">{{ $tick->ticket_number }}</span>
                                        <p class="text-xs font-semibold text-slate-900 dark:text-white group-hover:text-indigo-500 truncate">{{ $tick->title }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                                        <span>{{ $tick->category?->name ?? 'General' }}</span>
                                        <span>•</span>
                                        <span>By {{ $tick->raisedBy?->name }}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-lg font-bold {{ $tick->isOverdue() ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30' }}">
                                    {{ ucfirst($tick->priority) }} SLA
                                </span>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-slate-400">All support tickets within SLA parameters.</div>
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
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="size-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        <span>Spaces &amp; Projects Delivery Matrix</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">Real-time throughput and execution status across departmental spaces.</p>
                </div>
                <a href="{{ route('workspace.tasks', ['workspace' => $workspace->slug]) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>
                    Browse All Projects &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($spacesProgress as $sp)
                    <div class="p-5 rounded-3xl bg-gradient-to-br from-white to-slate-50/70 dark:from-zinc-900 dark:to-zinc-900/90 border border-slate-200/80 dark:border-zinc-800 shadow-xs hover:shadow-md hover:border-indigo-400 dark:hover:border-indigo-500/60 transition-all flex flex-col justify-between space-y-4 hover-lift">
                        <div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="size-3.5 rounded-full shadow-xs" style="background-color: {{ $sp['color'] }}"></span>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $sp['name'] }}</h4>
                                </div>
                                <span class="text-xs font-black text-slate-900 dark:text-white">{{ $sp['pct'] }}%</span>
                            </div>

                            <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-1">
                                {{ $sp['projects_count'] }} {{ Str::plural('Project', $sp['projects_count']) }} • {{ $sp['done'] }}/{{ $sp['total_tasks'] }} Tasks Done
                            </p>
                        </div>

                        <div class="space-y-2">
                            <div class="h-2.5 w-full bg-slate-100 dark:bg-zinc-800 rounded-full overflow-hidden p-0.5">
                                <div 
                                    class="h-full rounded-full transition-all duration-500 shadow-xs" 
                                    style="width: {{ $sp['pct'] }}%; background-color: {{ $sp['color'] }}"
                                ></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400">
                                <span>Status: <strong class="text-slate-600 dark:text-slate-300">{{ $sp['pct'] >= 60 ? 'On Schedule' : 'In Progress' }}</strong></span>
                                <a href="{{ route('workspace.tasks', ['workspace' => $workspace->slug, 'space' => $sp['id']]) }}" class="hover:underline font-semibold text-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400" wire:navigate>
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
    <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all hover-lift space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="size-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span>Combined Team Workload &amp; Capacity Heatmap</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-zinc-400">Cross-functional load balancing across sprint tasks and active support tickets.</p>
            </div>
            
            <a href="{{ route('workspace.tickets.capacity', ['workspace' => $workspace->slug]) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>
                Manage Agent Capacities &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1">
            @foreach($members->take(8) as $m)
                <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-gradient-to-br from-slate-50 to-white dark:from-zinc-800/50 dark:to-zinc-900/60 hover:border-indigo-400 dark:hover:border-indigo-500/60 hover:shadow-sm transition-all space-y-3 hover-lift-sm">
                    <div class="flex items-center gap-3">
                        <img src="{{ $m['avatar'] }}" class="size-10 rounded-full ring-2 ring-indigo-500/20 object-cover shrink-0" alt="{{ $m['name'] }}" />
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $m['name'] }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ $m['job_title'] }}</p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg uppercase tracking-wider {{ $m['status_color'] === 'emerald' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : ($m['status_color'] === 'yellow' ? 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30' : 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30') }}">
                            {{ $m['status_label'] }}
                        </span>
                    </div>

                    <!-- Meters -->
                    <div class="space-y-1.5 pt-1 border-t border-slate-100 dark:border-zinc-800">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">Active Tasks:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $m['active_tasks'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">Support Tickets:</span>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $m['active_tickets'] }} / {{ $m['capacity_limit'] }}</span>
                        </div>
                        
                        <div class="h-2 w-full bg-slate-200 dark:bg-zinc-700 rounded-full overflow-hidden p-0.5">
                            <div 
                                class="h-full rounded-full transition-all duration-500 {{ $m['ticket_load_pct'] >= 100 ? 'bg-rose-500' : ($m['ticket_load_pct'] >= 80 ? 'bg-amber-500' : 'bg-gradient-to-r from-indigo-500 to-violet-600') }}" 
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
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all hover-lift space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Task Status Distribution</span>
                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-200/60 dark:border-indigo-800/40">{{ $totalTasks }} total tasks</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                @foreach($statusCounts as $name => $data)
                    <div class="p-3 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/60 dark:from-zinc-800/60 dark:to-zinc-900/60 border border-slate-200/70 dark:border-zinc-800 space-y-1 hover-lift-sm transition-all">
                        <div class="flex items-center gap-1.5">
                            <span class="size-2 rounded-full" style="background-color: {{ $data['color'] }}"></span>
                            <span class="text-[10px] font-semibold text-slate-600 dark:text-zinc-400 truncate">{{ $name }}</span>
                        </div>
                        <p class="text-lg font-black text-slate-900 dark:text-white">{{ $data['count'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Visual Bar Segment -->
            <div class="h-3 w-full bg-slate-100 dark:bg-zinc-800 rounded-full overflow-hidden flex shadow-inner">
                @foreach($statusCounts as $name => $data)
                    @php $pct = $totalTasks > 0 ? ($data['count'] / $totalTasks) * 100 : 0; @endphp
                    @if($pct > 0)
                        <div class="h-full transition-all" style="width: {{ $pct }}%; background-color: {{ $data['color'] }}" title="{{ $name }}: {{ $data['count'] }}"></div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Ticket Priority & SLA Matrix -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all hover-lift space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Ticket Priority &amp; SLA Breakdown</span>
                <span class="text-xs font-semibold text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-950/60 px-2 py-0.5 rounded-lg border border-violet-200/60 dark:border-violet-800/40">{{ $totalTicketsCount }} total tickets</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                @foreach($ticketPriorityCounts as $pKey => $pData)
                    <div class="p-3 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/60 dark:from-zinc-800/60 dark:to-zinc-900/60 border border-slate-200/70 dark:border-zinc-800 space-y-1 hover-lift-sm transition-all">
                        <div class="flex items-center gap-1.5">
                            <span class="size-2 rounded-full" style="background-color: {{ $pData['color'] }}"></span>
                            <span class="text-[10px] font-semibold text-slate-600 dark:text-zinc-400 truncate">{{ $pData['label'] }}</span>
                        </div>
                        <p class="text-lg font-black text-slate-900 dark:text-white">{{ $pData['count'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Stacked Priority Bar -->
            <div class="h-3 w-full bg-slate-100 dark:bg-zinc-800 rounded-full overflow-hidden flex shadow-inner">
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
    <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-zinc-900 border border-slate-200/90 dark:border-zinc-800 shadow-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all hover-lift space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Workspace Real-Time Audit Feed</h3>
                <p class="text-xs text-slate-500 dark:text-zinc-400">Live operational log of all task progress and ticket support lifecycle changes.</p>
            </div>

            <!-- Feed Filter Tabs -->
            <div class="inline-flex items-center p-1 rounded-2xl bg-slate-100 dark:bg-zinc-800 border border-slate-200/80 dark:border-zinc-700/80 text-xs shadow-inner">
                <button 
                    wire:click="setActivityFilter('all')" 
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $activityFilter === 'all' ? 'bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-xs shadow-indigo-600/30' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}"
                >
                    All Events
                </button>
                <button 
                    wire:click="setActivityFilter('tasks')" 
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $activityFilter === 'tasks' ? 'bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-xs shadow-indigo-600/30' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}"
                >
                    Tasks
                </button>
                <button 
                    wire:click="setActivityFilter('tickets')" 
                    class="px-3.5 py-1.5 rounded-xl font-bold transition-all cursor-pointer {{ $activityFilter === 'tickets' ? 'bg-gradient-to-r from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-xs shadow-indigo-600/30' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}"
                >
                    Support
                </button>
            </div>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-zinc-800/80 max-h-96 overflow-y-auto pr-1">
            @forelse($recentActivities as $act)
                <div class="py-3.5 flex items-start gap-3.5 text-xs hover:bg-indigo-50/40 dark:hover:bg-zinc-800/40 -mx-2 px-2 rounded-xl transition-all hover-lift-sm">
                    <img 
                        src="{{ $act['user'] ? $act['user']->avatar() : 'https://ui-avatars.com/api/?name=System' }}" 
                        class="size-8 rounded-full mt-0.5 shrink-0 object-cover ring-2 ring-indigo-500/20" 
                        alt="" 
                    />
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $act['type'] === 'task' ? 'bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : 'bg-violet-500/15 text-violet-600 dark:text-violet-400 border border-violet-500/20' }}">
                                {{ strtoupper($act['type']) }}
                            </span>
                            <strong class="text-slate-900 dark:text-white">{{ $act['user']?->name ?? 'System' }}</strong>
                            <span class="text-slate-500 dark:text-zinc-400 truncate">{{ $act['description'] }}</span>
                        </div>

                        <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-1">
                            <span class="font-medium text-slate-600 dark:text-zinc-300 truncate">{{ $act['title'] }}</span>
                            <span>•</span>
                            <span>{{ $act['created_at']->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-xs text-slate-400">
                    No recent events found matching your filter.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Embedded Task Detail Slide-Over Modal -->
    <livewire:tasks.task-detail-modal />

    <!-- Embedded Ticket Detail Slide-Over Drawer -->
    <livewire:tickets.ticket-detail />

    <script>
        window.velocityTrendChart = function() {
            return {
                chart: null,
                init() {
                    const render = () => {
                        if (typeof window.Chart === 'undefined') {
                            setTimeout(render, 50);
                            return;
                        }
                        if (this.chart) {
                            try { this.chart.destroy(); } catch (e) {}
                            this.chart = null;
                        }
                        const canvas = this.$refs.canvas;
                        const dataEl = this.$refs.chartData;
                        if (!canvas || !dataEl) return;

                        let conf;
                        try {
                            conf = JSON.parse(dataEl.textContent);
                        } catch (e) {
                            return;
                        }

                        const ctx = canvas.getContext('2d');
                        const isDark = document.documentElement.classList.contains('dark');
                        
                        let influxBg = 'rgba(99, 102, 241, 0.85)';
                        let resolvedBg = 'rgba(16, 185, 129, 0.85)';
                        if (conf.chartType === 'line') {
                            const grad1 = ctx.createLinearGradient(0, 0, 0, 260);
                            grad1.addColorStop(0, 'rgba(99, 102, 241, 0.40)');
                            grad1.addColorStop(0.7, 'rgba(99, 102, 241, 0.08)');
                            grad1.addColorStop(1, 'rgba(99, 102, 241, 0.00)');
                            influxBg = grad1;

                            const grad2 = ctx.createLinearGradient(0, 0, 0, 260);
                            grad2.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
                            grad2.addColorStop(0.7, 'rgba(16, 185, 129, 0.06)');
                            grad2.addColorStop(1, 'rgba(16, 185, 129, 0.00)');
                            resolvedBg = grad2;
                        }

                        this.chart = new Chart(ctx, {
                            type: conf.chartType,
                            data: {
                                labels: conf.labels,
                                datasets: [
                                    {
                                        label: 'Tickets Influx',
                                        data: conf.ticketsCreated,
                                        borderColor: '#6366f1',
                                        backgroundColor: influxBg,
                                        fill: true,
                                        tension: 0.38,
                                        borderWidth: 2.5,
                                        pointRadius: 4,
                                        pointHoverRadius: 7,
                                        pointBackgroundColor: '#6366f1',
                                        pointBorderColor: isDark ? '#18181b' : '#ffffff',
                                        pointBorderWidth: 2,
                                    },
                                    {
                                        label: 'Tickets Resolved',
                                        data: conf.ticketsResolved,
                                        borderColor: '#10b981',
                                        backgroundColor: resolvedBg,
                                        fill: true,
                                        tension: 0.38,
                                        borderWidth: 2.5,
                                        pointRadius: 4,
                                        pointHoverRadius: 7,
                                        pointBackgroundColor: '#10b981',
                                        pointBorderColor: isDark ? '#18181b' : '#ffffff',
                                        pointBorderWidth: 2,
                                    },
                                    {
                                        label: 'Tasks Created',
                                        data: conf.tasksCreated,
                                        borderColor: '#38bdf8',
                                        backgroundColor: conf.chartType === 'line' ? 'rgba(56, 189, 248, 0.05)' : 'rgba(56, 189, 248, 0.85)',
                                        fill: false,
                                        borderDash: [4, 4],
                                        tension: 0.35,
                                        borderWidth: 2,
                                        pointRadius: 3,
                                        pointHoverRadius: 5,
                                        pointBackgroundColor: '#38bdf8',
                                    },
                                    {
                                        label: 'Tasks Completed',
                                        data: conf.tasksCompleted,
                                        borderColor: '#14b8a6',
                                        backgroundColor: conf.chartType === 'line' ? 'rgba(20, 184, 166, 0.05)' : 'rgba(20, 184, 166, 0.85)',
                                        fill: false,
                                        tension: 0.35,
                                        borderWidth: 2,
                                        pointRadius: 3,
                                        pointHoverRadius: 5,
                                        pointBackgroundColor: '#14b8a6',
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 1000,
                                    easing: 'easeOutQuart',
                                },
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            color: isDark ? '#a1a1aa' : '#52525b',
                                            font: { size: 11, weight: '600' },
                                            usePointStyle: true,
                                            boxWidth: 8,
                                            padding: 14,
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: isDark ? 'rgba(24, 24, 27, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                                        titleColor: isDark ? '#f4f4f5' : '#0f172a',
                                        bodyColor: isDark ? '#d4d4d8' : '#334155',
                                        borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                        borderWidth: 1,
                                        padding: 12,
                                        cornerRadius: 10,
                                        usePointStyle: true,
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                                        },
                                        ticks: {
                                            color: isDark ? '#71717a' : '#a1a1aa',
                                            font: { size: 10 }
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                                        },
                                        ticks: {
                                            color: isDark ? '#71717a' : '#a1a1aa',
                                            font: { size: 10 },
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        });
                    };

                    this.$nextTick(() => render());
                    window.addEventListener('chart-ready', () => this.$nextTick(() => render()), { once: true });
                }
            };
        };

        window.categoryDoughnutChart = function() {
            return {
                chart: null,
                init() {
                    const render = () => {
                        if (typeof window.Chart === 'undefined') {
                            setTimeout(render, 50);
                            return;
                        }
                        if (this.chart) {
                            try { this.chart.destroy(); } catch (e) {}
                            this.chart = null;
                        }
                        const canvas = this.$refs.canvas;
                        const dataEl = this.$refs.chartData;
                        if (!canvas || !dataEl) return;

                        let conf;
                        try {
                            conf = JSON.parse(dataEl.textContent);
                        } catch (e) {
                            return;
                        }

                        const ctx = canvas.getContext('2d');
                        const hasData = conf.data && conf.data.some(v => v > 0);
                        const isDark = document.documentElement.classList.contains('dark');
                        
                        this.chart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: conf.labels,
                                datasets: [{
                                    data: hasData ? conf.data : [1],
                                    backgroundColor: hasData ? [
                                        '#6366f1', '#8b5cf6', '#a855f7', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#14b8a6', '#64748b', '#06b6d4'
                                    ] : ['#3f3f46'],
                                    borderWidth: 2,
                                    borderColor: isDark ? '#18181b' : '#ffffff',
                                    hoverOffset: 12,
                                    borderRadius: 6,
                                    spacing: 2,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    animateScale: true,
                                    animateRotate: true,
                                    duration: 1100,
                                    easing: 'easeOutCirc'
                                },
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            color: isDark ? '#a1a1aa' : '#52525b',
                                            font: { size: 10, weight: '600' },
                                            boxWidth: 8,
                                            padding: 8
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: isDark ? 'rgba(24, 24, 27, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                                        titleColor: isDark ? '#f4f4f5' : '#0f172a',
                                        bodyColor: isDark ? '#d4d4d8' : '#334155',
                                        borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                        borderWidth: 1,
                                        padding: 10,
                                        cornerRadius: 8,
                                    }
                                },
                                cutout: '66%'
                            }
                        });
                    };

                    this.$nextTick(() => render());
                    window.addEventListener('chart-ready', () => this.$nextTick(() => render()), { once: true });
                }
            };
        };

        window.projectWorkloadChart = function() {
            return {
                chart: null,
                init() {
                    const render = () => {
                        if (typeof window.Chart === 'undefined') {
                            setTimeout(render, 50);
                            return;
                        }
                        if (this.chart) {
                            try { this.chart.destroy(); } catch (e) {}
                            this.chart = null;
                        }
                        const canvas = this.$refs.canvas;
                        const dataEl = this.$refs.chartData;
                        if (!canvas || !dataEl) return;

                        let conf;
                        try {
                            conf = JSON.parse(dataEl.textContent);
                        } catch (e) {
                            return;
                        }

                        const ctx = canvas.getContext('2d');
                        const isDark = document.documentElement.classList.contains('dark');

                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: conf.labels,
                                datasets: [
                                    {
                                        label: 'Active/Total Tasks',
                                        data: conf.tasks,
                                        backgroundColor: 'rgba(99, 102, 241, 0.85)',
                                        hoverBackgroundColor: 'rgba(99, 102, 241, 1)',
                                        borderColor: '#6366f1',
                                        borderWidth: 1,
                                        borderRadius: 8,
                                        borderSkipped: false,
                                    },
                                    {
                                        label: 'Incoming Tickets',
                                        data: conf.tickets,
                                        backgroundColor: 'rgba(139, 92, 246, 0.85)',
                                        hoverBackgroundColor: 'rgba(139, 92, 246, 1)',
                                        borderColor: '#8b5cf6',
                                        borderWidth: 1,
                                        borderRadius: 8,
                                        borderSkipped: false,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 1000,
                                    easing: 'easeOutQuart',
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            color: isDark ? '#a1a1aa' : '#52525b',
                                            font: { size: 11, weight: '600' }
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: isDark ? 'rgba(24, 24, 27, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                                        titleColor: isDark ? '#f4f4f5' : '#0f172a',
                                        bodyColor: isDark ? '#d4d4d8' : '#334155',
                                        borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                                        borderWidth: 1,
                                        padding: 10,
                                        cornerRadius: 8,
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                                        },
                                        ticks: {
                                            color: isDark ? '#e4e4e7' : '#27272a',
                                            font: { size: 11, weight: '500' }
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                                        },
                                        ticks: {
                                            precision: 0,
                                            color: isDark ? '#71717a' : '#a1a1aa',
                                            font: { size: 10 }
                                        }
                                    }
                                }
                            }
                        });
                    };

                    this.$nextTick(() => render());
                    window.addEventListener('chart-ready', () => this.$nextTick(() => render()), { once: true });
                }
            };
        };
    </script>
</div>
