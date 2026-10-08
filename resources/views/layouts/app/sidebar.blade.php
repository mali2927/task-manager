<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-950 font-sans antialiased selection:bg-indigo-500 selection:text-white">
        
        <!-- Sidebar -->
        @php
            $isRequester = auth()->user()->isWorkspaceRequester($currentWorkspace);
            $myTasksCount = auth()->user()->assignedTasks()->whereDoesntHave('status', fn ($q) => $q->where('type', 'done'))->count();
            $openTicketsCount = $currentWorkspace ? \App\Models\Ticket::where('workspace_id', $currentWorkspace->id)->where('status', 'open')->count() : 0;
            $pendingAccessCount = $currentWorkspace ? \App\Models\AccessRequest::where(fn ($q) => $q->where('workspace_id', $currentWorkspace->id)->orWhereNull('workspace_id'))->where('status', 'pending')->count() : 0;
            $myTicketsCount = $currentWorkspace ? \App\Models\Ticket::where('workspace_id', $currentWorkspace->id)->where(fn ($q) => $q->where('assigned_to_user_id', auth()->id())->orWhere('raised_by_user_id', auth()->id()))->whereNotIn('status', ['resolved', 'closed'])->count() : 0;
        @endphp
        <flux:sidebar sticky collapsible="mobile" class="border-e border-slate-200/80 dark:border-zinc-800/80 bg-gradient-to-b from-white via-slate-50/70 to-indigo-50/20 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 w-64 shadow-xs relative overflow-hidden backdrop-blur-md">
            <!-- Subtle Ambient Glow Orbs -->
            <div class="absolute -top-24 -left-24 size-52 bg-indigo-500/10 dark:bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 size-52 bg-purple-500/10 dark:bg-purple-500/15 rounded-full blur-3xl pointer-events-none"></div>
            
            <!-- Workspace Brand / Switcher Header -->
            <flux:sidebar.header class="pb-3 border-b border-slate-200/80 dark:border-zinc-800/80 mb-2 relative z-10">
                <div class="flex items-center gap-2.5 w-full p-2 rounded-2xl bg-white/80 dark:bg-zinc-900/80 border border-slate-200/70 dark:border-zinc-800/80 shadow-2xs backdrop-blur-xs group hover:border-indigo-300 dark:hover:border-zinc-700 transition-all">
                    <div class="relative shrink-0">
                        <img src="/stmu-logo.png" alt="STMU MIS" class="size-9 object-contain rounded-xl p-1 bg-white dark:bg-zinc-800 border border-slate-200/80 dark:border-zinc-700 shadow-2xs group-hover:scale-105 transition-transform" />
                        <span class="absolute -bottom-0.5 -right-0.5 size-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900 animate-pulse"></span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <h2 class="text-xs font-black text-slate-900 dark:text-white truncate tracking-tight">
                                STMU MIS
                            </h2>
                            <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md bg-gradient-to-r from-indigo-500/15 via-purple-500/15 to-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-500/30">
                                {{ $isRequester ? 'Requester' : 'Portal' }}
                            </span>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-zinc-400 block truncate font-medium mt-0.5 flex items-center gap-1.5">
                            <span class="size-1.5 rounded-full bg-indigo-500/70 shrink-0"></span>
                            <span class="truncate font-semibold text-slate-600 dark:text-zinc-300">{{ $currentWorkspace?->name ?? 'Task Manager' }}</span>
                        </span>
                    </div>

                    <flux:sidebar.collapse class="lg:hidden" />
                </div>
            </flux:sidebar.header>

            <!-- Main Navigation Group -->
            <flux:sidebar.nav class="space-y-4">
                @if(!$isRequester)
                    <div>
                        <div class="px-2 pb-1.5 flex items-center justify-between text-[10px] font-black tracking-wider text-slate-400 dark:text-zinc-500 uppercase">
                            <span class="flex items-center gap-1.5">
                                <span class="size-1.5 rounded-full bg-indigo-500"></span>
                                {{ __('Workspace') }}
                            </span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60">Core</span>
                        </div>

                        <div class="space-y-0.5">
                            <!-- Dashboard -->
                            @php $isDashboard = request()->routeIs('dashboard'); @endphp
                            <flux:sidebar.item 
                                :href="route('dashboard')" 
                                :current="$isDashboard" 
                                wire:navigate
                                class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isDashboard ? '!bg-gradient-to-r !from-indigo-500/15 !via-indigo-500/10 !to-transparent dark:!from-indigo-500/25 dark:!via-indigo-500/15 !border-indigo-200/90 dark:!border-indigo-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                            >
                                @if($isDashboard)
                                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-indigo-500 to-purple-600"></span>
                                @endif
                                <div class="flex items-center gap-2.5 w-full min-w-0">
                                    <span class="p-1.5 rounded-xl {{ $isDashboard ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/30' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-500/20 group-hover:scale-105' }} border border-indigo-500/20 transition-all shrink-0">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                    </span>
                                    <span class="font-bold text-xs {{ $isDashboard ? 'text-indigo-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                        {{ __('Dashboard') }}
                                    </span>
                                </div>
                            </flux:sidebar.item>

                            <!-- My Tasks -->
                            @php $isMyTasks = request()->routeIs('my-tasks'); @endphp
                            <flux:sidebar.item 
                                :href="route('my-tasks')" 
                                :current="$isMyTasks" 
                                wire:navigate
                                class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isMyTasks ? '!bg-gradient-to-r !from-emerald-500/15 !via-emerald-500/10 !to-transparent dark:!from-emerald-500/25 dark:!via-emerald-500/15 !border-emerald-200/90 dark:!border-emerald-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                            >
                                @if($isMyTasks)
                                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-emerald-500 to-teal-600"></span>
                                @endif
                                <div class="flex items-center justify-between w-full min-w-0 gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="p-1.5 rounded-xl {{ $isMyTasks ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-600/30' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-500/20 group-hover:scale-105' }} border border-emerald-500/20 transition-all shrink-0">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                        </span>
                                        <span class="font-bold text-xs {{ $isMyTasks ? 'text-emerald-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                            {{ __('My Tasks') }}
                                        </span>
                                    </div>
                                    @if($myTasksCount > 0)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shrink-0">
                                            {{ $myTasksCount }}
                                        </span>
                                    @endif
                                </div>
                            </flux:sidebar.item>

                            <!-- All Tasks (Board / List / Gantt) -->
                            @if($currentWorkspace)
                                @php $isTaskBoard = request()->routeIs('workspace.tasks') && !request()->has('space'); @endphp
                                <flux:sidebar.item 
                                    :href="route('workspace.tasks', ['workspace' => $currentWorkspace->slug])" 
                                    :current="$isTaskBoard" 
                                    wire:navigate
                                    class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isTaskBoard ? '!bg-gradient-to-r !from-sky-500/15 !via-sky-500/10 !to-transparent dark:!from-sky-500/25 dark:!via-sky-500/15 !border-sky-200/90 dark:!border-sky-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                                >
                                    @if($isTaskBoard)
                                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-sky-500 to-indigo-600"></span>
                                    @endif
                                    <div class="flex items-center gap-2.5 w-full min-w-0">
                                        <span class="p-1.5 rounded-xl {{ $isTaskBoard ? 'bg-sky-600 text-white shadow-xs shadow-sky-600/30' : 'bg-sky-500/10 text-sky-600 dark:text-sky-400 group-hover:bg-sky-500/20 group-hover:scale-105' }} border border-sky-500/20 transition-all shrink-0">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                                            </svg>
                                        </span>
                                        <span class="font-bold text-xs {{ $isTaskBoard ? 'text-sky-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                            {{ __('Task Board') }}
                                        </span>
                                    </div>
                                </flux:sidebar.item>

                                <!-- AI Assistant -->
                                @php $isAi = request()->routeIs('workspace.ai'); @endphp
                                <flux:sidebar.item 
                                    :href="route('workspace.ai', ['workspace' => $currentWorkspace->slug])" 
                                    :current="$isAi" 
                                    wire:navigate
                                    class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isAi ? '!bg-gradient-to-r !from-purple-500/15 !via-purple-500/10 !to-transparent dark:!from-purple-500/25 dark:!via-purple-500/15 !border-purple-200/90 dark:!border-purple-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                                >
                                    @if($isAi)
                                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-purple-500 to-indigo-600"></span>
                                    @endif
                                    <div class="flex items-center justify-between w-full min-w-0 gap-2">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="p-1.5 rounded-xl {{ $isAi ? 'bg-gradient-to-br from-purple-600 to-indigo-600 text-white shadow-xs shadow-purple-600/30' : 'bg-purple-500/10 text-purple-600 dark:text-purple-400 group-hover:bg-purple-500/20 group-hover:scale-105' }} border border-purple-500/20 transition-all shrink-0">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </span>
                                            <span class="font-bold text-xs {{ $isAi ? 'text-purple-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                                {{ __('AI Assistant') }}
                                            </span>
                                        </div>
                                        <span class="text-[9px] font-black tracking-wide px-2 py-0.5 rounded-full bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-600 text-white shadow-xs shadow-purple-500/30 shrink-0">
                                            Gemini
                                        </span>
                                    </div>
                                </flux:sidebar.item>

                                <!-- Teams & Members -->
                                @php $isTeams = request()->routeIs('workspace.teams'); @endphp
                                <flux:sidebar.item 
                                    :href="route('workspace.teams', ['workspace' => $currentWorkspace->slug])" 
                                    :current="$isTeams" 
                                    wire:navigate
                                    class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isTeams ? '!bg-gradient-to-r !from-blue-500/15 !via-blue-500/10 !to-transparent dark:!from-blue-500/25 dark:!via-blue-500/15 !border-blue-200/90 dark:!border-blue-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                                >
                                    @if($isTeams)
                                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-blue-500 to-indigo-600"></span>
                                    @endif
                                    <div class="flex items-center gap-2.5 w-full min-w-0">
                                        <span class="p-1.5 rounded-xl {{ $isTeams ? 'bg-blue-600 text-white shadow-xs shadow-blue-600/30' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400 group-hover:bg-blue-500/20 group-hover:scale-105' }} border border-blue-500/20 transition-all shrink-0">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </span>
                                        <span class="font-bold text-xs {{ $isTeams ? 'text-blue-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                            {{ __('Teams & People') }}
                                        </span>
                                    </div>
                                </flux:sidebar.item>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Support & Helpdesk -->
                @if($currentWorkspace)
                    <div>
                        <div class="px-2 pb-1.5 flex items-center justify-between text-[10px] font-black tracking-wider text-slate-400 dark:text-zinc-500 uppercase">
                            <span class="flex items-center gap-1.5">
                                <span class="size-1.5 rounded-full bg-purple-500"></span>
                                {{ $isRequester ? __('Support Tickets') : __('Support & Helpdesk') }}
                            </span>
                            @if($openTicketsCount > 0)
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/60">{{ $openTicketsCount }} Open</span>
                            @endif
                        </div>

                        <div class="space-y-0.5">
                            <!-- My Tickets -->
                            @php $isMyTickets = request()->routeIs('workspace.tickets.my'); @endphp
                            <flux:sidebar.item 
                                :href="route('workspace.tickets.my', ['workspace' => $currentWorkspace->slug])" 
                                :current="$isMyTickets" 
                                wire:navigate
                                class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isMyTickets ? '!bg-gradient-to-r !from-amber-500/15 !via-amber-500/10 !to-transparent dark:!from-amber-500/25 dark:!via-amber-500/15 !border-amber-200/90 dark:!border-amber-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                            >
                                @if($isMyTickets)
                                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-amber-500 to-orange-600"></span>
                                @endif
                                <div class="flex items-center justify-between w-full min-w-0 gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="p-1.5 rounded-xl {{ $isMyTickets ? 'bg-amber-600 text-white shadow-xs shadow-amber-600/30' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 group-hover:bg-amber-500/20 group-hover:scale-105' }} border border-amber-500/20 transition-all shrink-0">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                            </svg>
                                        </span>
                                        <span class="font-bold text-xs {{ $isMyTickets ? 'text-amber-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                            {{ __('My Tickets') }}
                                        </span>
                                    </div>
                                    @if($myTicketsCount > 0)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 shrink-0">
                                            {{ $myTicketsCount }}
                                        </span>
                                    @endif
                                </div>
                            </flux:sidebar.item>

                            <!-- Raise Ticket -->
                            @php $isRaiseTicket = request()->routeIs('workspace.tickets.raise'); @endphp
                            <flux:sidebar.item 
                                :href="route('workspace.tickets.raise', ['workspace' => $currentWorkspace->slug])" 
                                :current="$isRaiseTicket" 
                                wire:navigate
                                class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isRaiseTicket ? '!bg-gradient-to-r !from-rose-500/15 !via-rose-500/10 !to-transparent dark:!from-rose-500/25 dark:!via-rose-500/15 !border-rose-200/90 dark:!border-rose-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                            >
                                @if($isRaiseTicket)
                                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-rose-500 to-pink-600"></span>
                                @endif
                                <div class="flex items-center justify-between w-full min-w-0 gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="p-1.5 rounded-xl {{ $isRaiseTicket ? 'bg-rose-600 text-white shadow-xs shadow-rose-600/30' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 group-hover:bg-rose-500/20 group-hover:scale-105' }} border border-rose-500/20 transition-all shrink-0">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </span>
                                        <span class="font-bold text-xs {{ $isRaiseTicket ? 'text-rose-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                            {{ __('Raise Ticket') }}
                                        </span>
                                    </div>
                                    <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 shrink-0">
                                        + New
                                    </span>
                                </div>
                            </flux:sidebar.item>

                            @if(!$isRequester && auth()->user()->isWorkspaceAdmin($currentWorkspace))
                                <!-- Triage Queue -->
                                @php $isQueue = request()->routeIs('workspace.tickets.queue'); @endphp
                                <flux:sidebar.item 
                                    :href="route('workspace.tickets.queue', ['workspace' => $currentWorkspace->slug])" 
                                    :current="$isQueue" 
                                    wire:navigate
                                    class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isQueue ? '!bg-gradient-to-r !from-indigo-500/15 !via-indigo-500/10 !to-transparent dark:!from-indigo-500/25 dark:!via-indigo-500/15 !border-indigo-200/90 dark:!border-indigo-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                                >
                                    @if($isQueue)
                                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-indigo-500 to-purple-600"></span>
                                    @endif
                                    <div class="flex items-center justify-between w-full min-w-0 gap-2">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="p-1.5 rounded-xl {{ $isQueue ? 'bg-indigo-600 text-white shadow-xs shadow-indigo-600/30' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-500/20 group-hover:scale-105' }} border border-indigo-500/20 transition-all shrink-0">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </span>
                                            <span class="font-bold text-xs {{ $isQueue ? 'text-indigo-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                                {{ __('Triage Queue') }}
                                            </span>
                                        </div>
                                        @if($openTicketsCount > 0)
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-indigo-600 text-white shadow-xs shadow-indigo-600/30 shrink-0">
                                                {{ $openTicketsCount }}
                                            </span>
                                        @endif
                                    </div>
                                </flux:sidebar.item>

                                <!-- Team Capacity -->
                                @php $isCapacity = request()->routeIs('workspace.tickets.capacity'); @endphp
                                <flux:sidebar.item 
                                    :href="route('workspace.tickets.capacity', ['workspace' => $currentWorkspace->slug])" 
                                    :current="$isCapacity" 
                                    wire:navigate
                                    class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isCapacity ? '!bg-gradient-to-r !from-teal-500/15 !via-teal-500/10 !to-transparent dark:!from-teal-500/25 dark:!via-teal-500/15 !border-teal-200/90 dark:!border-teal-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                                >
                                    @if($isCapacity)
                                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-teal-500 to-emerald-600"></span>
                                    @endif
                                    <div class="flex items-center gap-2.5 w-full min-w-0">
                                        <span class="p-1.5 rounded-xl {{ $isCapacity ? 'bg-teal-600 text-white shadow-xs shadow-teal-600/30' : 'bg-teal-500/10 text-teal-600 dark:text-teal-400 group-hover:bg-teal-500/20 group-hover:scale-105' }} border border-teal-500/20 transition-all shrink-0">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </span>
                                        <span class="font-bold text-xs {{ $isCapacity ? 'text-teal-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                            {{ __('Team Capacity') }}
                                        </span>
                                    </div>
                                </flux:sidebar.item>

                                <!-- Categories & Routing -->
                                @php $isCategories = request()->routeIs('workspace.tickets.categories'); @endphp
                                <flux:sidebar.item 
                                    :href="route('workspace.tickets.categories', ['workspace' => $currentWorkspace->slug])" 
                                    :current="$isCategories" 
                                    wire:navigate
                                    class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isCategories ? '!bg-gradient-to-r !from-violet-500/15 !via-violet-500/10 !to-transparent dark:!from-violet-500/25 dark:!via-violet-500/15 !border-violet-200/90 dark:!border-violet-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                                >
                                    @if($isCategories)
                                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-violet-500 to-indigo-600"></span>
                                    @endif
                                    <div class="flex items-center gap-2.5 w-full min-w-0">
                                        <span class="p-1.5 rounded-xl {{ $isCategories ? 'bg-violet-600 text-white shadow-xs shadow-violet-600/30' : 'bg-violet-500/10 text-violet-600 dark:text-violet-400 group-hover:bg-violet-500/20 group-hover:scale-105' }} border border-violet-500/20 transition-all shrink-0">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </span>
                                        <span class="font-bold text-xs {{ $isCategories ? 'text-violet-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                            {{ __('Categories & Routing') }}
                                        </span>
                                    </div>
                                </flux:sidebar.item>

                                <!-- Access Requests -->
                                @php $isAccessRequests = request()->routeIs('workspace.access-requests'); @endphp
                                <flux:sidebar.item 
                                    :href="route('workspace.access-requests', ['workspace' => $currentWorkspace->slug])" 
                                    :current="$isAccessRequests" 
                                    wire:navigate
                                    class="relative !h-10 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isAccessRequests ? '!bg-gradient-to-r !from-amber-500/15 !via-amber-500/10 !to-transparent dark:!from-amber-500/25 dark:!via-amber-500/15 !border-amber-200/90 dark:!border-amber-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                                >
                                    @if($isAccessRequests)
                                        <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-gradient-to-b from-amber-500 to-orange-600"></span>
                                    @endif
                                    <div class="flex items-center justify-between w-full min-w-0 gap-2">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="p-1.5 rounded-xl {{ $isAccessRequests ? 'bg-amber-600 text-white shadow-xs shadow-amber-600/30' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 group-hover:bg-amber-500/20 group-hover:scale-105' }} border border-amber-500/20 transition-all shrink-0">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                </svg>
                                            </span>
                                            <span class="font-bold text-xs {{ $isAccessRequests ? 'text-amber-950 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">
                                                {{ __('Access Requests') }}
                                            </span>
                                        </div>
                                        @if($pendingAccessCount > 0)
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 shadow-xs shrink-0">
                                                {{ $pendingAccessCount }}
                                            </span>
                                        @endif
                                    </div>
                                </flux:sidebar.item>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Spaces Hierarchy Tree -->
                @if(!$isRequester && isset($workspaceSpaces) && $workspaceSpaces->count() > 0)
                    <div class="mt-4 pt-3 border-t border-slate-200/60 dark:border-zinc-800/60">
                        <div class="px-2 pb-2 flex items-center justify-between text-[10px] font-black tracking-wider text-slate-400 dark:text-zinc-500 uppercase">
                            <span class="flex items-center gap-1.5">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                {{ __('Spaces & Folders') }}
                            </span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 border border-slate-200/60 dark:border-zinc-700/60">{{ $workspaceSpaces->count() }}</span>
                        </div>

                        <div class="space-y-0.5">
                            @foreach($workspaceSpaces as $sp)
                                @php
                                    $isSpaceActive = request()->fullUrlIs('*space=' . $sp->id . '*');
                                @endphp
                                <flux:sidebar.item 
                                    :href="route('workspace.tasks', ['workspace' => $currentWorkspace->slug, 'space' => $sp->id])"
                                    :current="$isSpaceActive"
                                    wire:navigate
                                    class="relative !h-9.5 px-2.5 my-0.5 rounded-xl transition-all duration-150 group {{ $isSpaceActive ? '!bg-gradient-to-r !from-indigo-500/15 !via-indigo-500/10 !to-transparent dark:!from-indigo-500/25 dark:!via-indigo-500/15 !border-indigo-200/90 dark:!border-indigo-500/30 !shadow-2xs' : '!border-transparent hover:!bg-slate-100/80 dark:hover:!bg-zinc-800/60' }}"
                                >
                                    @if($isSpaceActive)
                                        <span class="absolute left-0 top-1.5 bottom-1.5 w-1 rounded-r-full" style="background-color: {{ $sp->color }};"></span>
                                    @endif
                                    <div class="flex items-center justify-between w-full min-w-0 gap-2">
                                        <span class="truncate flex items-center gap-2 min-w-0">
                                            <span class="size-2.5 rounded-md shrink-0 shadow-2xs ring-2 ring-white/80 dark:ring-zinc-800/80" style="background-color: {{ $sp->color }};"></span>
                                            <span class="font-bold text-xs {{ $isSpaceActive ? 'text-slate-900 dark:text-white' : 'text-slate-700 dark:text-zinc-300 group-hover:text-slate-900 dark:group-hover:text-white' }} truncate">{{ $sp->name }}</span>
                                        </span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800/80 text-slate-500 dark:text-zinc-400 border border-slate-200/60 dark:border-zinc-700/60 shrink-0 group-hover:bg-white dark:group-hover:bg-zinc-700 transition-colors">
                                            {{ $sp->projects->count() }}
                                        </span>
                                    </div>
                                </flux:sidebar.item>
                            @endforeach
                        </div>
                    </div>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <!-- User Menu -->
            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Top Header for Desktop & Mobile -->
        <flux:header class="border-b border-zinc-200 bg-white/80 dark:border-zinc-800 dark:bg-zinc-900/80 backdrop-blur-xs sticky top-0 z-40 px-4 lg:px-6">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <!-- Search Quick Action -->
            @if(!$isRequester && $currentWorkspace)
                <div class="hidden sm:flex items-center gap-2 max-w-md w-full ml-2">
                    <a 
                        href="{{ route('workspace.ai', ['workspace' => $currentWorkspace->slug]) }}" 
                        class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 text-xs text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 w-64 transition-colors"
                        wire:navigate
                    >
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <span>Ask Gemini or search...</span>
                        <kbd class="ml-auto text-[10px] font-mono px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300">AI</kbd>
                    </a>
                </div>
            @endif

            <flux:spacer />

            <!-- In-App Notification Center Bell Component -->
            <livewire:notifications.notification-bell :workspace="$currentWorkspace" />

            <!-- Profile Dropdown (Mobile) -->
            <flux:dropdown position="top" align="end" class="lg:hidden">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
