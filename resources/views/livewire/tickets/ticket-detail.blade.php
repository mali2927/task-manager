<div>
    @if($isOpen && $ticket)
        <!-- Backdrop -->
        <div 
            class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 transition-opacity"
            wire:click="close"
        ></div>

        <!-- Slide-over Drawer -->
        <div class="fixed inset-y-0 right-0 max-w-3xl w-full bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-50 flex flex-col overflow-hidden animate-in slide-in-from-right duration-200">
            
            <!-- Header Bar -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 shrink-0">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 tracking-tight">
                        {{ $ticket->ticket_number }}
                    </span>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                        {{ $ticket->category?->name ?? 'General Support' }}
                    </span>
                    @if($ticket->project)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                            <svg class="size-3 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                            {{ $ticket->project->name }}
                        </span>
                    @endif

                    @if($ticket->isOverdue())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-500/10 text-red-500 border border-red-500/20 animate-pulse">
                            <span class="size-1.5 rounded-full bg-red-500"></span> OVERDUE SLA
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if($isStaff && !$ticket->task)
                        <button 
                            wire:click="openConvertToTaskModal" 
                            type="button" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/80 dark:hover:bg-indigo-900/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 shadow-2xs transition-colors cursor-pointer"
                            title="Convert ticket into engineering task"
                        >
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                            <span>Convert to Task</span>
                        </button>
                    @endif

                    <!-- Close button -->
                    <button 
                        wire:click="close" 
                        type="button" 
                        class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                    >
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <!-- Body Content (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                <!-- Linked Task Banner if already converted -->
                @if($ticket->task)
                    <div class="p-3.5 rounded-xl bg-gradient-to-r from-sky-500/10 via-indigo-500/10 to-transparent border border-sky-300/60 dark:border-sky-800/60 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="p-1.5 rounded-lg bg-sky-600 text-white shrink-0 shadow-2xs">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            </span>
                            <div class="min-w-0">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400">Linked Engineering Deliverable</div>
                                <div class="font-bold text-zinc-900 dark:text-white truncate">Task #{{ $ticket->task->id }} — {{ $ticket->task->title }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase" style="background-color: {{ $ticket->task->status?->color }}20; color: {{ $ticket->task->status?->color }}">
                                {{ $ticket->task->status?->name }}
                            </span>
                            <button 
                                wire:click="$dispatch('open-task-detail', { taskId: {{ $ticket->task->id }} })"
                                type="button" 
                                class="px-3 py-1.5 rounded-lg text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white shadow-xs cursor-pointer"
                            >
                                Open Task →
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Subject Title -->
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white leading-tight">
                        {{ $ticket->subject }}
                    </h2>
                    <div class="flex items-center gap-2 mt-1.5 text-xs text-zinc-500">
                        <span>Raised by <strong class="text-zinc-800 dark:text-zinc-200">{{ $ticket->raisedBy?->name }}</strong></span>
                        <span>•</span>
                        <span>{{ $ticket->created_at->format('M d, Y H:i') }} ({{ $ticket->created_at->diffForHumans() }})</span>
                    </div>
                </div>

                <!-- Meta Properties Row (Status, Priority, Project, Team, Assignee) -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
                    <!-- Status -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Status</label>
                        @if($isStaff || auth()->id() === $ticket->raised_by_user_id)
                            <select 
                                wire:change="updateStatus($event.target.value)" 
                                class="w-full text-xs font-semibold rounded-lg bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1.5 px-2 focus:ring-indigo-500"
                            >
                                @foreach(['open' => 'Open', 'assigned' => 'Assigned', 'in_progress' => 'In Progress', 'on_hold' => 'On Hold', 'resolved' => 'Resolved', 'closed' => 'Closed', 'reopened' => 'Reopened'] as $stKey => $stLabel)
                                    <option value="{{ $stKey }}" {{ $stKey === $ticket->status ? 'selected' : '' }}>
                                        {{ $stLabel }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="text-xs font-semibold capitalize text-zinc-800 dark:text-zinc-200 mt-1">
                                {{ str_replace('_', ' ', $ticket->status) }}
                            </div>
                        @endif
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Priority</label>
                        @if($isStaff)
                            <select 
                                wire:change="updatePriority($event.target.value)" 
                                class="w-full text-xs font-semibold rounded-lg bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1.5 px-2 focus:ring-indigo-500"
                            >
                                @foreach(['urgent' => 'Urgent (4h)', 'high' => 'High (24h)', 'normal' => 'Normal (3d)', 'low' => 'Low (5d)'] as $pKey => $pLabel)
                                    <option value="{{ $pKey }}" {{ $pKey === $ticket->priority ? 'selected' : '' }}>
                                        {{ $pLabel }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="text-xs font-semibold capitalize text-zinc-800 dark:text-zinc-200 mt-1">
                                {{ $ticket->priority }}
                            </div>
                        @endif
                    </div>

                    <!-- Related Project -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Project</label>
                        @if($isStaff || $canManageAssignment)
                            <select 
                                wire:change="updateProject($event.target.value ?: null)" 
                                class="w-full text-xs font-semibold rounded-lg bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1.5 px-2 focus:ring-indigo-500"
                            >
                                <option value="">None</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ $proj->id == $ticket->project_id ? 'selected' : '' }}>
                                        {{ $proj->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-1 truncate">
                                {{ $ticket->project?->name ?? 'None' }}
                            </div>
                        @endif
                    </div>

                    <!-- Assigned Team -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Assigned Team</label>
                        @if($canManageAssignment)
                            <select 
                                wire:change="onTeamSelected($event.target.value ?: null)" 
                                class="w-full text-xs font-semibold rounded-lg bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1.5 px-2 focus:ring-indigo-500"
                            >
                                <option value="">No Team</option>
                                @foreach($teams as $tm)
                                    <option value="{{ $tm->id }}" {{ $tm->id == $assignedTeamId ? 'selected' : '' }}>
                                        {{ $tm->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-1">
                                {{ $ticket->assignedTeam?->name ?? 'Unassigned' }}
                            </div>
                        @endif
                    </div>

                    <!-- Assigned Person with Capacity Badge -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Assignee</label>
                        @if($canManageAssignment && $assignedTeamId)
                            <select 
                                wire:change="onAssigneeSelected($event.target.value ?: null)" 
                                class="w-full text-xs font-semibold rounded-lg bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1.5 px-2 focus:ring-indigo-500"
                            >
                                <option value="">Unassigned</option>
                                @foreach($teamMembersCapacity as $cap)
                                    <option value="{{ $cap['user_id'] }}" {{ $cap['user_id'] == $assignedToUserId ? 'selected' : '' }}>
                                        {{ $cap['name'] }} ({{ $cap['active_tickets'] }}/{{ $cap['capacity_limit'] }} tickets)
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-1">
                                {{ $ticket->assignedTo?->name ?? 'Unassigned' }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SLA & Due Date Banner -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-200/60 dark:border-zinc-800 text-xs">
                    <div class="flex items-center gap-2">
                        <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-zinc-500">SLA Resolution Target:</span>
                        <strong class="{{ $ticket->isOverdue() ? 'text-red-500 font-bold' : 'text-zinc-800 dark:text-zinc-200' }}">
                            {{ $ticket->due_by ? $ticket->due_by->format('M d, Y H:i') : 'Not Set' }}
                            @if($ticket->due_by)
                                ({{ $ticket->due_by->diffForHumans() }})
                            @endif
                        </strong>
                    </div>

                    @if($ticket->status === 'resolved' && $ticket->canBeReopened() && auth()->id() === $ticket->raised_by_user_id)
                        <button 
                            wire:click="reopenTicket"
                            class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 border border-rose-500/30 transition-colors cursor-pointer"
                        >
                            Reopen Ticket
                        </button>
                    @endif
                </div>

                <!-- Customer Satisfaction (CSAT) Card if Resolved / Closed -->
                @if(in_array($ticket->status, ['resolved', 'closed']))
                    <div class="p-4 rounded-xl bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-transparent border border-amber-300/60 dark:border-amber-800/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-amber-700 dark:text-amber-300 font-bold text-xs uppercase tracking-wider">
                                <svg class="size-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>Support Experience & Satisfaction (CSAT)</span>
                            </div>

                            @if($ticket->hasRating())
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white">
                                    {{ $ticket->rating }} / 5 Stars
                                </span>
                            @endif
                        </div>

                        @if($ticket->hasRating())
                            <!-- Recorded Rating -->
                            <div class="flex items-center gap-1 text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="size-4 {{ $i <= $ticket->rating ? 'text-amber-400 fill-amber-400' : 'text-zinc-300 dark:text-zinc-700' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="text-xs text-zinc-600 dark:text-zinc-300 ml-2 font-medium">
                                    {{ $ticket->rating_feedback ?: 'No written feedback provided.' }}
                                </span>
                            </div>
                        @else
                            <!-- Submit Rating Form -->
                            <div class="space-y-2 pt-1">
                                <p class="text-xs text-zinc-600 dark:text-zinc-300">How would you rate the speed and quality of resolution for this ticket?</p>
                                
                                <div class="flex items-center gap-2">
                                    @for($star = 1; $star <= 5; $star++)
                                        <button 
                                            wire:click="submitCsatRating({{ $star }})"
                                            type="button" 
                                            class="p-1 hover:scale-125 transition-transform text-zinc-300 hover:text-amber-400 cursor-pointer"
                                            title="Rate {{ $star }} Stars"
                                        >
                                            <svg class="size-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </button>
                                    @endfor
                                    <span class="text-[11px] text-zinc-400 ml-2">Click stars to rate (1 = Poor, 5 = Excellent)</span>
                                </div>

                                <div class="flex items-center gap-2 pt-1">
                                    <input 
                                        type="text" 
                                        wire:model="csatFeedback" 
                                        placeholder="Optional feedback: Was anything outstanding or missing?" 
                                        class="flex-1 text-xs px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200"
                                    />
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Resolution Summary Box if Resolved -->
                @if($ticket->status === 'resolved' && $ticket->resolution_summary)
                    <div class="p-4 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 space-y-1">
                        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300 font-bold text-xs uppercase tracking-wider">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Resolution Summary</span>
                        </div>
                        <p class="text-xs text-emerald-900 dark:text-emerald-200 leading-relaxed whitespace-pre-wrap">
                            {{ $ticket->resolution_summary }}
                        </p>
                    </div>
                @endif

                <!-- Description Section -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Issue Description</label>
                    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800 text-xs text-zinc-800 dark:text-zinc-200 leading-relaxed whitespace-pre-wrap">
                        {{ $ticket->description }}
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">
                            Attachments ({{ $ticket->attachments->count() }})
                        </label>

                        <label class="text-xs font-semibold text-indigo-500 hover:text-indigo-400 cursor-pointer flex items-center gap-1">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            <span>Add File</span>
                            <input type="file" wire:model="newAttachmentFile" wire:change="uploadAttachment" class="hidden" />
                        </label>
                    </div>

                    @if($ticket->attachments->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($ticket->attachments as $att)
                                <div class="flex items-center justify-between p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-800/40 text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="size-4 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                        <div class="min-w-0">
                                            <div class="font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $att->file_name }}</div>
                                            <div class="text-[10px] text-zinc-400">{{ $att->formattedSize() }}</div>
                                        </div>
                                    </div>
                                    <a href="{{ Storage::disk('public')->url($att->file_path) }}" target="_blank" download class="text-indigo-500 hover:text-indigo-400 text-xs font-bold shrink-0 ml-2">
                                        Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-zinc-400 italic">No attachments uploaded yet.</p>
                    @endif
                </div>

                <!-- Conversation Thread -->
                <div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Conversation &amp; Notes</h3>
                    </div>

                    <!-- New Comment Input Box -->
                    <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 space-y-3">
                        <!-- Canned Responses Toolbar for Staff -->
                        @if($isStaff)
                            <div class="flex items-center gap-1.5 flex-wrap pb-1 border-b border-zinc-200/60 dark:border-zinc-700/60">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Quick Templates:</span>
                                <button wire:click="applyCannedResponse('need_info')" type="button" class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-200/60 dark:bg-zinc-700 hover:bg-indigo-100 hover:text-indigo-700 dark:hover:bg-indigo-900 text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">
                                    Need More Info
                                </button>
                                <button wire:click="applyCannedResponse('investigating')" type="button" class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-200/60 dark:bg-zinc-700 hover:bg-indigo-100 hover:text-indigo-700 dark:hover:bg-indigo-900 text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">
                                    Investigating
                                </button>
                                <button wire:click="applyCannedResponse('fix_deployed')" type="button" class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-200/60 dark:bg-zinc-700 hover:bg-indigo-100 hover:text-indigo-700 dark:hover:bg-indigo-900 text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">
                                    Fix Deployed
                                </button>
                                <button wire:click="applyCannedResponse('scheduled')" type="button" class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-200/60 dark:bg-zinc-700 hover:bg-indigo-100 hover:text-indigo-700 dark:hover:bg-indigo-900 text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer">
                                    Scheduled Sprint
                                </button>
                            </div>
                        @endif

                        <textarea 
                            wire:model="newCommentBody" 
                            rows="3" 
                            placeholder="{{ $isInternalNote ? 'Write an internal staff note (hidden from customer)...' : 'Post a public reply visible to the requester...' }}"
                            class="w-full text-xs px-3 py-2 rounded-xl bg-white dark:bg-zinc-800 border {{ $isInternalNote ? 'border-amber-500/50 focus:border-amber-500' : 'border-zinc-200 dark:border-zinc-700 focus:border-indigo-500' }} text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-hidden focus:ring-1 transition-all resize-none"
                        ></textarea>

                        <div class="flex items-center justify-between">
                            <!-- Internal Note Toggle (Staff Only) -->
                            @if($isStaff)
                                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold {{ $isInternalNote ? 'text-amber-500 font-bold' : 'text-zinc-500' }}">
                                    <input type="checkbox" wire:model.live="isInternalNote" class="rounded text-amber-500 focus:ring-amber-500" />
                                    <span>🔒 Internal Note (Staff Only)</span>
                                </label>
                            @else
                                <div></div>
                            @endif

                            <button 
                                wire:click="addComment" 
                                type="button" 
                                class="px-4 py-1.5 rounded-lg text-xs font-bold text-white transition-colors cursor-pointer {{ $isInternalNote ? 'bg-amber-600 hover:bg-amber-500' : 'bg-indigo-600 hover:bg-indigo-500' }}"
                            >
                                {{ $isInternalNote ? 'Post Staff Note' : 'Send Reply' }}
                            </button>
                        </div>
                    </div>

                    <!-- Comments List -->
                    <div class="space-y-3">
                        @foreach($ticket->comments as $comment)
                            {{-- Hide internal notes from non-staff/requesters --}}
                            @if(!$comment->is_internal_note || $isStaff)
                                <div class="p-3.5 rounded-xl border {{ $comment->is_internal_note ? 'bg-amber-50/60 dark:bg-amber-950/20 border-amber-300 dark:border-amber-800/50' : 'bg-white dark:bg-zinc-800/50 border-zinc-200/80 dark:border-zinc-800' }} space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="size-6 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 font-bold text-[10px] flex items-center justify-center">
                                                {{ substr($comment->user?->name ?? 'U', 0, 1) }}
                                            </div>
                                            <span class="text-xs font-semibold text-zinc-900 dark:text-white">
                                                {{ $comment->user?->name }}
                                            </span>
                                            @if($comment->is_internal_note)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30 uppercase tracking-wider">
                                                    Staff Only Note
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-zinc-400">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-zinc-800 dark:text-zinc-200 leading-relaxed whitespace-pre-wrap pl-8">
                                        {{ $comment->body }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Audit Log Section -->
                <div class="space-y-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">
                            Activity Audit Trail ({{ $ticket->activityLogs->count() }})
                        </label>
                        <a 
                            href="{{ route('workspace.activity-logs', ['workspace' => $ticket->workspace->slug, 'search' => $ticket->ticket_number]) }}" 
                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
                            wire:navigate
                        >
                            Open Full Audit Hub →
                        </a>
                    </div>

                    <div class="space-y-2">
                        @foreach($ticket->activityLogs->take(10) as $log)
                            <div class="p-2.5 rounded-lg bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-800/50 flex items-center justify-between gap-3 text-[11px]">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="size-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                                    <strong class="text-zinc-900 dark:text-white shrink-0">{{ $log->user?->name ?? 'System' }}</strong>
                                    <span class="text-zinc-500 dark:text-zinc-400 truncate">{{ $log->description }}</span>
                                </div>
                                <span class="text-[10px] text-zinc-400 shrink-0 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Convert Ticket to Task Modal -->
        @if($showConvertToTaskModal)
            <div class="fixed inset-0 bg-black/70 backdrop-blur-xs z-60 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Convert Ticket to Task</h3>
                                <p class="text-[11px] text-zinc-400">Create an engineering deliverable from {{ $ticket->ticket_number }}</p>
                            </div>
                        </div>
                        <button wire:click="$set('showConvertToTaskModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="convertToTask" class="space-y-3.5 text-xs">
                        <!-- Task Title -->
                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Deliverable Title <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                wire:model="targetTaskTitle" 
                                class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                                required
                            />
                            @error('targetTaskTitle') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <!-- Target Project & Task List -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Target Project</label>
                                <select 
                                    wire:change="onTargetProjectChanged($event.target.value ?: null)" 
                                    class="w-full text-xs px-2.5 py-1.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                                >
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}" {{ $p->id == $targetProjectId ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Target Task List <span class="text-red-500">*</span></label>
                                <select 
                                    wire:model="targetTaskListId" 
                                    class="w-full text-xs px-2.5 py-1.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                                    required
                                >
                                    @foreach($workspaceSpaces as $sp)
                                        <optgroup label="{{ $sp->name }}">
                                            @foreach($sp->projects as $pr)
                                                @foreach($pr->lists as $ls)
                                                    <option value="{{ $ls->id }}">{{ $pr->name }} &gt; {{ $ls->name }}</option>
                                                @endforeach
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('targetTaskListId') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Priority & Due Date -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Priority</label>
                                <select 
                                    wire:model="targetTaskPriority" 
                                    class="w-full text-xs px-2.5 py-1.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                                >
                                    <option value="urgent">🚨 Urgent</option>
                                    <option value="high">🔶 High</option>
                                    <option value="normal">🔷 Normal</option>
                                    <option value="low">⚪ Low</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Due Date</label>
                                <input 
                                    type="date" 
                                    wire:model="targetTaskDueDate" 
                                    class="w-full text-xs px-2.5 py-1.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                                />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Task Description</label>
                            <textarea 
                                wire:model="targetTaskDescription" 
                                rows="3" 
                                class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 resize-none"
                            ></textarea>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                            <button 
                                wire:click="$set('showConvertToTaskModal', false)" 
                                type="button" 
                                class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs cursor-pointer"
                            >
                                Create Task &amp; Link
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Resolve Ticket Modal -->
        @if($showResolveModal)
            <div class="fixed inset-0 bg-black/70 backdrop-blur-xs z-60 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Resolve Ticket {{ $ticket->ticket_number }}</h3>
                        <button wire:click="$set('showResolveModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Please provide a clear resolution summary explaining how the issue was fixed. This will be sent to the requester.
                    </p>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Resolution Summary <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            wire:model="resolutionSummary"
                            rows="4"
                            placeholder="Explain the root cause and actions taken to resolve this ticket..."
                            class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 resize-none"
                            required
                        ></textarea>
                        @error('resolutionSummary') <span class="text-[11px] text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button 
                            wire:click="$set('showResolveModal', false)"
                            class="px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="confirmResolution"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs cursor-pointer"
                        >
                            Mark as Resolved
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
