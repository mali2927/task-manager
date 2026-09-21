<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header & KPIs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white">Support Triage Queue</h1>
                @if($openCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                        {{ $openCount }} Unassigned
                    </span>
                @endif
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Review and triage incoming support tickets. Route tickets to teams with automated capacity balancing.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('workspace.tickets.capacity', ['workspace' => $workspace->slug]) }}" 
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition-colors"
                wire:navigate
            >
                <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                <span>Team Capacity</span>
            </a>
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

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Open in Triage -->
        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between shadow-xs">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Open in Triage</span>
                <div class="text-2xl font-black text-zinc-900 dark:text-white mt-0.5">{{ $openCount }}</div>
                <div class="text-[11px] text-zinc-500 mt-0.5">Awaiting team assignment</div>
            </div>
            <div class="size-10 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" /></svg>
            </div>
        </div>

        <!-- In Progress -->
        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between shadow-xs">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Active Working</span>
                <div class="text-2xl font-black text-zinc-900 dark:text-white mt-0.5">{{ $assignedCount }}</div>
                <div class="text-[11px] text-zinc-500 mt-0.5">Assigned to team members</div>
            </div>
            <div class="size-10 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- Overdue SLA -->
        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border {{ $overdueCount > 0 ? 'border-red-500/40 bg-red-500/5' : 'border-zinc-200 dark:border-zinc-800' }} flex items-center justify-between shadow-xs">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider {{ $overdueCount > 0 ? 'text-red-500' : 'text-zinc-400' }}">Overdue SLA</span>
                <div class="text-2xl font-black {{ $overdueCount > 0 ? 'text-red-500' : 'text-zinc-900 dark:text-white' }} mt-0.5">{{ $overdueCount }}</div>
                <div class="text-[11px] text-zinc-500 mt-0.5">Passed resolution deadline</div>
            </div>
            <div class="size-10 rounded-xl {{ $overdueCount > 0 ? 'bg-red-500/20 text-red-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400' }} flex items-center justify-center">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
        </div>
    </div>

    @if($toastMessage)
        <div class="p-3.5 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs flex items-center justify-between">
            <span>{{ $toastMessage }}</span>
            <button wire:click="$set('toastMessage', null)" class="text-xs font-bold hover:underline cursor-pointer">Dismiss</button>
        </div>
    @endif

    <!-- Controls Row: Tabs & Filters -->
    <div class="space-y-3 bg-zinc-50 dark:bg-zinc-900/60 p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3">
            <!-- Tabs -->
            <div class="flex flex-wrap items-center gap-1">
                @foreach([
                    'open' => 'Triage Queue',
                    'assigned' => 'Assigned',
                    'in_progress' => 'In Progress',
                    'resolved' => 'Resolved',
                    'closed' => 'Closed',
                    'all' => 'All Tickets'
                ] as $k => $label)
                    <button 
                        wire:click="$set('statusTab', '{{ $k }}')"
                        type="button" 
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all cursor-pointer {{ $statusTab === $k ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-xs font-semibold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' }}"
                    >
                        {{ $label }}
                        @if($k === 'open' && $openCount > 0)
                            <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-indigo-500/20 text-indigo-500">{{ $openCount }}</span>
                        @endif
                    </button>
                @endforeach
            </div>

            <!-- Search input -->
            <div class="w-full lg:w-64">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search ticket # or subject..." 
                    class="w-full text-xs px-3 py-1.5 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-hidden focus:ring-1 focus:ring-indigo-500"
                />
            </div>
        </div>

        <!-- Filter Selectors -->
        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-zinc-200/60 dark:border-zinc-800 text-xs">
            <span class="text-[11px] text-zinc-400 font-medium">Filter by:</span>

            <!-- Category Filter -->
            <select wire:model.live="filterCategoryId" class="text-xs px-2.5 py-1 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Priority Filter -->
            <select wire:model.live="filterPriority" class="text-xs px-2.5 py-1 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300">
                <option value="">All Priorities</option>
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="normal">Normal</option>
                <option value="low">Low</option>
            </select>

            <!-- SLA Filter Toggle -->
            <button 
                wire:click="$set('filterSla', '{{ $filterSla === 'overdue' ? 'all' : 'overdue' }}')"
                type="button" 
                class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors cursor-pointer {{ $filterSla === 'overdue' ? 'bg-red-500 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700' }}"
            >
                ⚠️ Overdue Only
            </button>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50 dark:bg-zinc-800/60 text-zinc-700 dark:text-zinc-300 font-semibold border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-5 py-3">Ticket</th>
                        <th class="px-4 py-3">Subject &amp; Requester</th>
                        <th class="px-4 py-3">Team &amp; Assignee</th>
                        <th class="px-4 py-3">SLA Due By</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($tickets as $t)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors {{ $t->isOverdue() ? 'bg-red-500/5' : '' }}">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button 
                                        wire:click="$dispatch('open-ticket-detail', { ticketId: {{ $t->id }} })"
                                        class="font-black text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer"
                                    >
                                        {{ $t->ticket_number }}
                                    </button>
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold border uppercase tracking-wider {{ $t->priorityBadgeColor() }}">
                                        {{ $t->priority }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-zinc-400 mt-0.5">
                                    {{ $t->category?->name ?? 'General' }}
                                </div>
                            </td>

                            <td class="px-4 py-3.5 max-w-sm">
                                <div 
                                    wire:click="$dispatch('open-ticket-detail', { ticketId: {{ $t->id }} })"
                                    class="font-semibold text-zinc-900 dark:text-white truncate hover:text-indigo-500 cursor-pointer"
                                >
                                    {{ $t->subject }}
                                </div>
                                <div class="text-[11px] text-zinc-500 mt-0.5">
                                    From <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $t->raisedBy?->name }}</span>
                                    • {{ $t->created_at->diffForHumans() }}
                                </div>
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($t->assignedTeam)
                                    <div class="flex items-center gap-1.5">
                                        <span class="size-2 rounded-full" style="background-color: {{ $t->assignedTeam->color }}"></span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $t->assignedTeam->name }}</span>
                                    </div>
                                    <div class="text-[11px] text-zinc-500 mt-0.5">
                                        {{ $t->assignedTo?->name ?? 'Unassigned' }}
                                    </div>
                                @else
                                    <span class="text-zinc-400 italic">No team assigned</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($t->due_by)
                                    <div class="{{ $t->isOverdue() ? 'text-red-500 font-bold' : 'text-zinc-700 dark:text-zinc-300' }}">
                                        {{ $t->due_by->format('M d, H:i') }}
                                    </div>
                                    <div class="text-[10px] {{ $t->isOverdue() ? 'text-red-500 font-semibold' : 'text-zinc-400' }}">
                                        {{ $t->due_by->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $t->statusBadgeColor() }}">
                                    {{ ucfirst(str_replace('_', ' ', $t->status)) }}
                                </span>
                            </td>

                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        wire:click="openAssignModal({{ $t->id }})"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30 transition-colors cursor-pointer"
                                    >
                                        {{ $t->assigned_team_id ? 'Reassign' : 'Assign Team' }}
                                    </button>
                                    <button 
                                        wire:click="$dispatch('open-ticket-detail', { ticketId: {{ $t->id }} })"
                                        class="px-2.5 py-1 rounded-lg text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors cursor-pointer"
                                    >
                                        View
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-zinc-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="size-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium">No tickets found in this queue.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    <!-- Quick Assignment Modal with Capacity Indicator -->
    @if($showAssignModal && $assignTicket)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-indigo-500">{{ $assignTicket->ticket_number }}</span>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Route to Team &amp; Assignee</h3>
                    </div>
                    <button wire:click="$set('showAssignModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800 text-xs">
                    <div class="font-semibold text-zinc-900 dark:text-white truncate">{{ $assignTicket->subject }}</div>
                    <div class="text-zinc-500 text-[11px] mt-0.5">Priority: <strong class="capitalize">{{ $assignTicket->priority }}</strong></div>
                </div>

                <!-- Select Team -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                        Select Team <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:change="onTeamChanged($event.target.value ?: null)"
                        class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                    >
                        <option value="">Choose a team...</option>
                        @foreach($teams as $t)
                            <option value="{{ $t->id }}" {{ $t->id == $selectedTeamId ? 'selected' : '' }}>
                                {{ $t->name }} ({{ $t->members->count() }} members)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Capacity Breakdown & Assignee Selection -->
                @if($selectedTeamId && count($teamMembersCapacity) > 0)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                                Team Workload &amp; Capacity
                            </label>
                            @if($recommendedMemberName)
                                <span class="text-[10px] font-bold text-emerald-500">
                                    ★ Suggested: {{ $recommendedMemberName }}
                                </span>
                            @endif
                        </div>

                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($teamMembersCapacity as $cap)
                                <label class="flex items-center justify-between p-2.5 rounded-xl border {{ $cap['user_id'] == $selectedAssigneeId ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/20' : 'border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/40' }} cursor-pointer transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <input 
                                            type="radio" 
                                            wire:model="selectedAssigneeId" 
                                            value="{{ $cap['user_id'] }}"
                                            class="text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <div>
                                            <div class="text-xs font-semibold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                                <span>{{ $cap['name'] }}</span>
                                                @if($cap['user_id'] == $teamMembersCapacity[0]['user_id'])
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">Best Match</span>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-zinc-500">{{ ucfirst($cap['role']) }}</div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <div class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                                            {{ $cap['active_tickets'] }}/{{ $cap['capacity_limit'] }} tickets
                                        </div>
                                        <div class="w-20 bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 mt-1 overflow-hidden">
                                            <div class="h-1.5 rounded-full {{ $cap['is_over_capacity'] ? 'bg-red-500' : ($cap['is_at_capacity'] ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $cap['load_percentage']) }}%"></div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @elseif($selectedTeamId)
                    <div class="p-3 text-center text-xs text-zinc-400 italic bg-zinc-50 dark:bg-zinc-800/20 rounded-xl">
                        This team has no assigned members yet.
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <button 
                        wire:click="$set('showAssignModal', false)"
                        class="px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="confirmAssignment"
                        class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs cursor-pointer"
                    >
                        Confirm Assignment
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Slide-over Ticket Detail Drawer -->
    <livewire:tickets.ticket-detail />
</div>
