<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white">Workload &amp; Capacity Dashboard</h1>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $workspaceLoadPercentage > 85 ? 'bg-red-500/10 text-red-500' : 'bg-emerald-500/10 text-emerald-500' }} border border-current">
                    {{ $workspaceLoadPercentage }}% Workspace Load
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Monitor team ticket loads in real time. Define max concurrent tickets per member to prevent burnout and ensure fair routing.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('workspace.tickets.queue', ['workspace' => $workspace->slug]) }}" 
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition-colors"
                wire:navigate
            >
                <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                <span>Triage Queue</span>
            </a>
            <a 
                href="{{ route('workspace.tickets.categories', ['workspace' => $workspace->slug]) }}" 
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 transition-colors"
                wire:navigate
            >
                <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                <span>Categories &amp; Routing</span>
            </a>
        </div>
    </div>

    @if($feedbackMessage)
        <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs flex items-center justify-between">
            <span>{{ $feedbackMessage }}</span>
            <button wire:click="$set('feedbackMessage', null)" class="text-xs font-bold hover:underline cursor-pointer">Dismiss</button>
        </div>
    @endif

    <!-- Workspace Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Active Tickets</span>
            <div class="text-2xl font-black text-zinc-900 dark:text-white mt-0.5">{{ $totalActiveTickets }}</div>
            <div class="text-[11px] text-zinc-500 mt-0.5">Currently assigned to team members</div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Capacity Slots</span>
            <div class="text-2xl font-black text-zinc-900 dark:text-white mt-0.5">{{ $totalWorkspaceCapacity }}</div>
            <div class="text-[11px] text-zinc-500 mt-0.5">Max concurrent ticket bandwidth</div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Workload Health</span>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="text-2xl font-black {{ $workspaceLoadPercentage > 85 ? 'text-red-500' : 'text-emerald-500' }}">
                    {{ $workspaceLoadPercentage }}%
                </span>
                <span class="text-xs font-semibold text-zinc-500">
                    {{ $workspaceLoadPercentage > 85 ? 'High Load' : ($workspaceLoadPercentage > 50 ? 'Optimal' : 'Light Load') }}
                </span>
            </div>
            <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="h-1.5 rounded-full {{ $workspaceLoadPercentage > 85 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ min(100, $workspaceLoadPercentage) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Team Workload Cards -->
    <div class="space-y-6">
        @forelse($teamCapacities as $teamData)
            @php
                $team = $teamData['team'];
                $members = $teamData['members'];
            @endphp
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 shadow-xs space-y-4">
                <!-- Team Card Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2.5">
                        <span class="size-3 rounded-full" style="background-color: {{ $team->color }}"></span>
                        <h2 class="text-base font-bold text-zinc-900 dark:text-white">{{ $team->name }}</h2>
                        <span class="text-xs text-zinc-400">({{ $members->count() }} members)</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-xs text-right">
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $teamData['total_active_tickets'] }} / {{ $teamData['total_capacity'] }} tickets</span>
                            <span class="text-[11px] text-zinc-400 ml-1">({{ $teamData['overall_percentage'] }}% capacity)</span>
                        </div>
                        <div class="w-24 bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $teamData['overall_percentage'] > 90 ? 'bg-red-500' : ($teamData['overall_percentage'] > 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $teamData['overall_percentage']) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Members Capacity Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($members as $m)
                        <div class="p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="size-8 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ substr($m['name'], 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-xs text-zinc-900 dark:text-white truncate">{{ $m['name'] }}</div>
                                        <div class="text-[10px] text-zinc-400 truncate">{{ ucfirst($m['role']) }}</div>
                                    </div>
                                </div>

                                <button 
                                    wire:click="openEditLimitModal({{ $team->id }}, {{ $m['user_id'] }}, '{{ $m['name'] }}', {{ $m['capacity_limit'] }})"
                                    class="p-1 rounded-md text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 hover:bg-zinc-200/60 dark:hover:bg-zinc-700 transition-colors text-[10px] font-semibold flex items-center gap-1 cursor-pointer"
                                    title="Edit Capacity Limit"
                                >
                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    <span>Limit</span>
                                </button>
                            </div>

                            <!-- Progress Bar -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-zinc-500">Active Load:</span>
                                    <span class="font-bold text-zinc-800 dark:text-zinc-200">
                                        {{ $m['active_tickets'] }} / {{ $m['capacity_limit'] }} tickets
                                    </span>
                                </div>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full {{ $m['is_over_capacity'] ? 'bg-red-500' : ($m['is_at_capacity'] ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $m['load_percentage']) }}%"></div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex items-center justify-between pt-1">
                                @if($m['is_over_capacity'])
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/10 text-red-500 border border-red-500/20">
                                        Overloaded ({{ $m['load_percentage'] }}%)
                                    </span>
                                @elseif($m['is_at_capacity'])
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                        At Capacity (100%)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                        Available ({{ $m['load_percentage'] }}%)
                                    </span>
                                @endif

                                <span class="text-[10px] text-zinc-400">
                                    Max: {{ $m['capacity_limit'] }} tickets
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full p-4 text-center text-xs text-zinc-400 italic">
                            No members assigned to this team.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-zinc-500 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                <p class="font-medium text-sm">No teams configured in this workspace.</p>
                <p class="text-xs text-zinc-400 mt-1">Create teams in Teams &amp; People to enable capacity-based routing.</p>
            </div>
        @endforelse
    </div>

    <!-- Edit Capacity Limit Modal -->
    @if($showEditLimitModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Adjust Capacity Limit</h3>
                    <button wire:click="$set('showEditLimitModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Set maximum concurrent active tickets for <strong class="text-zinc-800 dark:text-zinc-200">{{ $editingUserName }}</strong>.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Max Concurrent Tickets</label>
                    <input 
                        type="number" 
                        wire:model="newCapacityLimit"
                        min="1"
                        max="50"
                        class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                    />
                    @error('newCapacityLimit') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button 
                        wire:click="$set('showEditLimitModal', false)"
                        class="px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="saveCapacityLimit"
                        class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs cursor-pointer"
                    >
                        Save Limit
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Slide-over Ticket Detail Drawer -->
    <livewire:tickets.ticket-detail />
</div>
