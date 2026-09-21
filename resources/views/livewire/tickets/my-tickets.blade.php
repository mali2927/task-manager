<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-zinc-900 dark:text-white">My Support Tickets</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Track issues assigned to you or monitor tickets you've raised across the workspace.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->user()->isWorkspaceAdmin($workspace))
                <a 
                    href="{{ route('workspace.tickets.queue', ['workspace' => $workspace->slug]) }}" 
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition-colors"
                    wire:navigate
                >
                    <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    <span>Triage Queue</span>
                </a>
            @endif

            <a 
                href="{{ route('workspace.tickets.raise', ['workspace' => $workspace->slug]) }}" 
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs transition-colors"
                wire:navigate
            >
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Raise Ticket</span>
            </a>
        </div>
    </div>

    <!-- View Switcher Tabs -->
    <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2">
        <button 
            wire:click="$set('viewMode', 'assigned')" 
            class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer {{ $viewMode === 'assigned' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' }}"
        >
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            <span>Assigned to Me</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $assignedTotalCount > 0 ? 'bg-indigo-500 text-white' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400' }}">
                {{ $assignedTotalCount }}
            </span>
        </button>

        <button 
            wire:click="$set('viewMode', 'raised')" 
            class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer {{ $viewMode === 'raised' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' }}"
        >
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            <span>Raised by Me</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $raisedTotalCount > 0 ? 'bg-indigo-500 text-white' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400' }}">
                {{ $raisedTotalCount }}
            </span>
        </button>
    </div>

    <!-- Groups Layout -->
    <div class="space-y-6">
        <!-- 1. Active Working (In Progress & Assigned) -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="size-2 rounded-full bg-amber-500"></span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">Active Working</h3>
                    <span class="text-xs font-semibold text-zinc-400">({{ $inProgress->count() }})</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($inProgress as $t)
                    <div 
                        wire:click="$dispatch('open-ticket-detail', { ticketId: {{ $t->id }} })"
                        class="p-4 rounded-xl bg-white dark:bg-zinc-900 border {{ $t->isOverdue() ? 'border-red-500/40 bg-red-500/5' : 'border-zinc-200 dark:border-zinc-800' }} hover:border-indigo-500/50 hover:shadow-md transition-all cursor-pointer space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded text-[11px] font-black bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                {{ $t->ticket_number }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold border uppercase tracking-wider {{ $t->priorityBadgeColor() }}">
                                    {{ $t->priority }}
                                </span>
                                @if($t->isOverdue())
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-red-500 text-white">OVERDUE</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white line-clamp-2 leading-snug">
                                {{ $t->subject }}
                            </h4>
                            <p class="text-xs text-zinc-500 line-clamp-2 mt-1">
                                {{ $t->description }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/80 text-[11px] text-zinc-500">
                            <span class="truncate">
                                @if($viewMode === 'assigned')
                                    By {{ $t->raisedBy?->name }}
                                @else
                                    Assigned to {{ $t->assignedTo?->name ?? 'Team Queue' }}
                                @endif
                            </span>
                            <span class="{{ $t->isOverdue() ? 'text-red-500 font-bold' : '' }}">
                                {{ $t->due_by ? $t->due_by->diffForHumans() : 'No SLA' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-6 text-center text-xs text-zinc-400 bg-zinc-50/50 dark:bg-zinc-800/20 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">
                        No active tickets in progress.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. Open / Triage Tickets -->
        @if($open->count() > 0)
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="size-2 rounded-full bg-indigo-500"></span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">Open in Triage</h3>
                    <span class="text-xs font-semibold text-zinc-400">({{ $open->count() }})</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($open as $t)
                        <div 
                            wire:click="$dispatch('open-ticket-detail', { ticketId: {{ $t->id }} })"
                            class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-indigo-500/50 hover:shadow-md transition-all cursor-pointer space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded text-[11px] font-black bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                    {{ $t->ticket_number }}
                                </span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold border uppercase tracking-wider {{ $t->priorityBadgeColor() }}">
                                    {{ $t->priority }}
                                </span>
                            </div>

                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white line-clamp-2 leading-snug">
                                {{ $t->subject }}
                            </h4>

                            <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/80 text-[11px] text-zinc-500">
                                <span>Awaiting review</span>
                                <span>{{ $t->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. Resolved & Closed -->
        @if($resolved->count() > 0)
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="size-2 rounded-full bg-emerald-500"></span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">Resolved &amp; Closed</h3>
                    <span class="text-xs font-semibold text-zinc-400">({{ $resolved->count() }})</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($resolved as $t)
                        <div 
                            wire:click="$dispatch('open-ticket-detail', { ticketId: {{ $t->id }} })"
                            class="p-4 rounded-xl bg-zinc-50/60 dark:bg-zinc-900/40 border border-zinc-200/60 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all cursor-pointer space-y-2 opacity-80 hover:opacity-100"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400">{{ $t->ticket_number }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </div>

                            <h4 class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate">
                                {{ $t->subject }}
                            </h4>

                            <div class="text-[11px] text-zinc-400 pt-1">
                                Resolved {{ $t->resolved_at?->diffForHumans() ?? $t->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Slide-over Ticket Detail Drawer -->
    <livewire:tickets.ticket-detail />
</div>
