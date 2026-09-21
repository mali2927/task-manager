<div>
    @if($isOpen && $task)
        <!-- Backdrop -->
        <div 
            class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 transition-opacity"
            wire:click="close"
        ></div>

        <!-- Slide-over Drawer -->
        <div class="fixed inset-y-0 right-0 max-w-3xl w-full bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl z-50 flex flex-col overflow-hidden animate-in slide-in-from-right duration-200">
            
            <!-- Header Bar -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        {{ $task->taskList?->project?->space?->name }} / {{ $task->taskList?->project?->name }} / {{ $task->taskList?->name }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <!-- AI Summarize Button -->
                    <button 
                        wire:click="generateAiSummary" 
                        wire:loading.attr="disabled"
                        type="button" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30 transition-all cursor-pointer shadow-xs"
                    >
                        <span wire:loading.remove wire:target="generateAiSummary">
                            <svg class="size-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </span>
                        <span wire:loading wire:target="generateAiSummary" class="animate-spin size-3.5 border-2 border-indigo-500 border-t-transparent rounded-full"></span>
                        <span>AI Summarize</span>
                    </button>

                    <!-- Delete button -->
                    <button 
                        wire:confirm="Are you sure you want to delete this task?"
                        wire:click="deleteTask" 
                        type="button" 
                        class="p-1.5 rounded-lg text-zinc-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors"
                        title="Delete Task"
                    >
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>

                    <!-- Close button -->
                    <button 
                        wire:click="close" 
                        type="button" 
                        class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                    >
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <!-- Body Content (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                <!-- AI Summary Card if present -->
                @if($aiSummary)
                    <div class="p-4 rounded-xl border border-indigo-200 dark:border-indigo-900/60 bg-indigo-50/60 dark:bg-indigo-950/30 space-y-2 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-indigo-700 dark:text-indigo-300 font-semibold text-xs uppercase tracking-wider">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                                <span>Gemini AI Task Digest</span>
                            </div>
                            <button wire:click="$set('aiSummary', null)" class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">Dismiss</button>
                        </div>
                        <div class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed prose dark:prose-invert max-w-none">
                            {!! Str::markdown($aiSummary) !!}
                        </div>
                    </div>
                @endif

                <!-- Title Input -->
                <div>
                    <input 
                        type="text" 
                        wire:model="title" 
                        wire:blur="updateTitle" 
                        class="w-full text-xl font-bold text-zinc-900 dark:text-white bg-transparent border-0 border-b border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 focus:border-indigo-500 focus:ring-0 px-1 py-1 rounded-sm transition-all"
                        placeholder="Task title..."
                    />
                </div>

                <!-- Meta Properties Row (Status, Priority, Dates, Time) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
                    <!-- Status -->
                    <div>
                        <label class="block text-[11px] font-medium uppercase tracking-wider text-zinc-400 mb-1">Status</label>
                        <select 
                            wire:change="updateStatus($event.target.value)" 
                            class="w-full text-xs font-medium rounded-lg bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5 focus:ring-indigo-500"
                        >
                            @foreach($allStatuses as $st)
                                <option value="{{ $st->id }}" {{ $st->id == $task->status_id ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-[11px] font-medium uppercase tracking-wider text-zinc-400 mb-1">Priority</label>
                        <select 
                            wire:change="updatePriority($event.target.value)" 
                            class="w-full text-xs font-medium rounded-lg bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5 focus:ring-indigo-500"
                        >
                            <option value="urgent" {{ $task->priority == 'urgent' ? 'selected' : '' }}>🚨 Urgent</option>
                            <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>🔶 High</option>
                            <option value="normal" {{ $task->priority == 'normal' ? 'selected' : '' }}>🔷 Normal</option>
                            <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>⚪ Low</option>
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-[11px] font-medium uppercase tracking-wider text-zinc-400 mb-1">Due Date</label>
                        <input 
                            type="date" 
                            wire:model="dueDate" 
                            wire:change="updateDates"
                            class="w-full text-xs rounded-lg bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1.5 px-2 focus:ring-indigo-500"
                        />
                    </div>

                    <!-- Time Tracker Widget -->
                    <div>
                        <label class="block text-[11px] font-medium uppercase tracking-wider text-zinc-400 mb-1">Time Logged</label>
                        <div class="flex items-center gap-1.5">
                            <button 
                                wire:click="toggleTimer" 
                                type="button" 
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium {{ $isTimerRunning ? 'bg-rose-500 text-white animate-pulse' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600' }} transition-colors"
                            >
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>{{ $isTimerRunning ? 'Stop' : 'Start' }}</span>
                            </button>
                            <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">{{ $task->actual_hours }}h</span>
                        </div>
                    </div>
                </div>

                <!-- Assignees Row -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Assignees</label>
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach($task->assignees as $assignee)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                <img src="{{ $assignee->avatar() }}" class="size-4 rounded-full" alt="" />
                                <span>{{ $assignee->name }}</span>
                                <button wire:click="toggleAssignee({{ $assignee->id }})" class="text-zinc-400 hover:text-red-500">&times;</button>
                            </span>
                        @endforeach

                        <!-- Assignee Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button 
                                @click="open = !open" 
                                type="button" 
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium border border-dashed border-zinc-300 dark:border-zinc-700 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors"
                            >
                                <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                <span>Assign</span>
                            </button>

                            <div 
                                x-show="open" 
                                @click.outside="open = false" 
                                class="absolute left-0 mt-2 w-48 rounded-lg bg-white dark:bg-zinc-800 shadow-lg border border-zinc-200 dark:border-zinc-700 py-1 z-50 text-xs"
                                style="display: none;"
                            >
                                @foreach($workspaceUsers as $u)
                                    <button 
                                        wire:click="toggleAssignee({{ $u->id }})" 
                                        @click="open = false"
                                        class="w-full text-left px-3 py-1.5 flex items-center gap-2 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300"
                                    >
                                        <img src="{{ $u->avatar() }}" class="size-4 rounded-full" alt="" />
                                        <span class="truncate">{{ $u->name }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Description</label>
                    <textarea 
                        wire:model="description" 
                        wire:blur="updateDescription" 
                        rows="4" 
                        class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/60 p-3 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden"
                        placeholder="Write detailed task notes or markdown..."
                    ></textarea>
                </div>

                <!-- Checklists & Subtasks Section -->
                <div class="space-y-4 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">
                            Checklists ({{ $task->checklistStats()['completed'] }}/{{ $task->checklistStats()['total'] }})
                        </h4>
                        @if($task->checklistStats()['total'] > 0)
                            <div class="w-24 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $task->checklistStats()['percent'] }}%"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Checklist Items -->
                    <div class="space-y-1.5">
                        @foreach($task->checklists as $chk)
                            <div class="flex items-center justify-between group px-2 py-1.5 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <label class="flex items-center gap-2.5 cursor-pointer text-xs flex-1">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleChecklist({{ $chk->id }})" 
                                        {{ $chk->is_completed ? 'checked' : '' }} 
                                        class="rounded border-zinc-300 dark:border-zinc-700 text-indigo-600 focus:ring-indigo-500 size-4"
                                    />
                                    <span class="{{ $chk->is_completed ? 'line-through text-zinc-400 dark:text-zinc-500' : 'text-zinc-800 dark:text-zinc-200' }}">
                                        {{ $chk->title }}
                                    </span>
                                </label>
                                <button 
                                    wire:click="deleteChecklist({{ $chk->id }})" 
                                    class="opacity-0 group-hover:opacity-100 text-zinc-400 hover:text-red-500 text-xs px-1 transition-opacity"
                                >
                                    &times;
                                </button>
                            </div>
                        @endforeach

                        <!-- Add checklist input -->
                        <form wire:submit.prevent="addChecklist" class="flex items-center gap-2 pt-1">
                            <input 
                                type="text" 
                                wire:model="newChecklistTitle" 
                                placeholder="+ Add item to checklist..." 
                                class="flex-1 text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-transparent px-3 py-1.5 text-zinc-800 dark:text-zinc-200 focus:ring-indigo-500"
                            />
                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200">Add</button>
                        </form>
                    </div>
                </div>

                <!-- Subtasks Section -->
                <div class="space-y-3 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">
                        Subtasks ({{ $task->subtasks->count() }})
                    </h4>

                    <div class="space-y-1.5">
                        @foreach($task->subtasks as $sub)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/60 text-xs">
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $sub->title }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                                    {{ $sub->status?->name }}
                                </span>
                            </div>
                        @endforeach

                        <!-- Add subtask input -->
                        <form wire:submit.prevent="addSubtask" class="flex items-center gap-2">
                            <input 
                                type="text" 
                                wire:model="newSubtaskTitle" 
                                placeholder="+ Add nested subtask..." 
                                class="flex-1 text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-transparent px-3 py-1.5 text-zinc-800 dark:text-zinc-200 focus:ring-indigo-500"
                            />
                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200">Add</button>
                        </form>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="space-y-3 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">
                            Attachments ({{ $task->attachments->count() }})
                        </h4>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($task->attachments as $att)
                            <div class="p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 flex items-center justify-between text-xs">
                                <div class="min-w-0 flex items-center gap-2">
                                    <svg class="size-4 shrink-0 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    <div class="truncate">
                                        <p class="truncate font-medium text-zinc-800 dark:text-zinc-200">{{ $att->file_name }}</p>
                                        <span class="text-[10px] text-zinc-400">{{ $att->formattedSize() }}</span>
                                    </div>
                                </div>
                                <button wire:click="deleteAttachment({{ $att->id }})" class="text-zinc-400 hover:text-red-500 text-xs ml-1">&times;</button>
                            </div>
                        @endforeach
                    </div>

                    <!-- Upload Input -->
                    <div class="flex items-center gap-2">
                        <input type="file" wire:model="uploadedFile" class="text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:bg-indigo-50 dark:file:bg-indigo-950 file:text-indigo-600" />
                        @if($uploadedFile)
                            <button wire:click="uploadAttachment" class="text-xs px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">Save</button>
                        @endif
                    </div>
                </div>

                <!-- Comments & Discussion (Threaded) -->
                <div class="space-y-4 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400">
                        Activity & Comments
                    </h4>

                    <!-- New comment form -->
                    <div class="space-y-2">
                        @if($replyToCommentId)
                            <div class="flex items-center justify-between text-[11px] text-indigo-500 bg-indigo-50 dark:bg-indigo-950/40 px-2.5 py-1 rounded-md">
                                <span>Replying to comment...</span>
                                <button wire:click="cancelReply" class="hover:underline">Cancel</button>
                            </div>
                        @endif
                        <div class="flex gap-2">
                            <textarea 
                                wire:model="newCommentContent" 
                                rows="2" 
                                placeholder="Add a comment or @mention team member..." 
                                class="flex-1 text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/60 p-2.5 text-zinc-800 dark:text-zinc-200 focus:ring-indigo-500"
                            ></textarea>
                            <button 
                                wire:click="addComment" 
                                type="button" 
                                class="self-end px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium transition-colors"
                            >
                                Post
                            </button>
                        </div>
                    </div>

                    <!-- Comment List -->
                    <div class="space-y-3 pt-2">
                        @foreach($task->comments as $comment)
                            <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/80 space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $comment->user->avatar() }}" class="size-5 rounded-full" alt="" />
                                        <span class="font-semibold text-zinc-900 dark:text-white">{{ $comment->user->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-zinc-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        <button wire:click="replyTo({{ $comment->id }})" class="text-[10px] text-indigo-600 dark:text-indigo-400 hover:underline">Reply</button>
                                    </div>
                                </div>
                                <p class="text-zinc-700 dark:text-zinc-300 leading-relaxed">{{ $comment->content }}</p>

                                <!-- Threaded Replies -->
                                @if($comment->replies->count() > 0)
                                    <div class="ml-4 pl-3 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-2 pt-1">
                                        @foreach($comment->replies as $reply)
                                            <div class="space-y-1">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-1.5">
                                                        <img src="{{ $reply->user->avatar() }}" class="size-4 rounded-full" alt="" />
                                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $reply->user->name }}</span>
                                                    </div>
                                                    <span class="text-[10px] text-zinc-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-zinc-600 dark:text-zinc-400 text-xs">{{ $reply->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Activity Audit Log -->
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/80 space-y-2">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Audit History</span>
                        <div class="space-y-1 text-[11px] text-zinc-500 dark:text-zinc-400">
                            @foreach($task->activities->take(6) as $act)
                                <div class="flex items-center justify-between py-1 border-b border-zinc-100/50 dark:border-zinc-800/40">
                                    <span>
                                        <strong class="text-zinc-700 dark:text-zinc-300">{{ $act->user?->name ?? 'System' }}</strong>: {{ $act->description }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400 shrink-0">{{ $act->created_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
