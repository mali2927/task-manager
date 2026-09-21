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
                <div class="flex items-center gap-2.5">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 tracking-tight">
                        {{ $ticket->ticket_number }}
                    </span>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                        {{ $ticket->category?->name ?? 'General Support' }}
                    </span>

                    @if($ticket->isOverdue())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-500/10 text-red-500 border border-red-500/20 animate-pulse">
                            <span class="size-1.5 rounded-full bg-red-500"></span> OVERDUE SLA
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
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

                <!-- Meta Properties Row (Status, Priority, Team, Assignee) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
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
                    <label class="block text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">
                        Activity Audit Trail
                    </label>

                    <div class="space-y-2">
                        @foreach($ticket->activityLogs->take(8) as $log)
                            <div class="flex items-center gap-2 text-[11px] text-zinc-500">
                                <span class="size-1.5 rounded-full bg-zinc-400"></span>
                                <strong>{{ $log->user?->name ?? 'System' }}</strong>
                                <span>{{ $log->action }}:</span>
                                <span class="text-zinc-700 dark:text-zinc-300">{{ $log->to_value ?? $log->action }}</span>
                                <span class="text-[10px] text-zinc-400 ml-auto">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

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
