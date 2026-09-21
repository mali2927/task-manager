<div class="h-[calc(100vh-130px)] flex -m-6 bg-zinc-50/60 dark:bg-zinc-950 overflow-hidden">
    
    <!-- LEFT PANE: CHAT HISTORY SIDEBAR -->
    <div class="w-72 sm:w-80 border-r border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex flex-col shrink-0">
        
        <!-- Sidebar Header / New Chat Action -->
        <div class="p-3.5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                Chat History
            </span>

            <button 
                wire:click="startNewChat" 
                type="button" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-all cursor-pointer"
            >
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>New Chat</span>
            </button>
        </div>

        <!-- Saved Conversations List (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            @forelse($conversations as $convo)
                <div 
                    wire:click="selectConversation({{ $convo->id }})" 
                    class="group flex items-center justify-between p-2.5 rounded-xl text-xs cursor-pointer transition-all {{ $activeConversationId === $convo->id ? 'bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200/80 dark:border-indigo-800/80 text-indigo-900 dark:text-indigo-200 font-semibold shadow-xs' : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 border border-transparent' }}"
                >
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <svg class="size-4 shrink-0 {{ $activeConversationId === $convo->id ? 'text-indigo-600 dark:text-indigo-400' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        <div class="truncate">
                            <p class="truncate">{{ $convo->title }}</p>
                            <span class="text-[10px] text-zinc-400 font-normal">{{ $convo->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Delete button -->
                    <button 
                        wire:confirm="Delete this chat history?"
                        wire:click.stop="deleteConversation({{ $convo->id }})" 
                        type="button" 
                        class="opacity-0 group-hover:opacity-100 p-1 text-zinc-400 hover:text-rose-500 rounded-md transition-opacity"
                        title="Delete chat"
                    >
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            @empty
                <div class="py-12 px-4 text-center text-xs text-zinc-400">
                    <svg class="size-8 mx-auto text-zinc-300 dark:text-zinc-700 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    <p class="font-medium">No chat history yet</p>
                    <p class="text-[11px] mt-1">Start a conversation to see your chats saved here.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- RIGHT PANE: ACTIVE CHAT CONVERSATION -->
    <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-zinc-950">
        
        <!-- Chat Top Header -->
        <div class="px-6 py-3.5 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shadow-xs shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="size-8 rounded-xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 text-white flex items-center justify-center shadow-xs shrink-0">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xs font-bold text-zinc-900 dark:text-white truncate">
                        {{ $activeConversation ? $activeConversation->title : 'New AI Consultation' }}
                    </h2>
                    <span class="text-[10px] text-zinc-400 block truncate">
                        Grounded in {{ $workspace->name }} tasks, statuses & assignments
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($userTier === 'director')
                    <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 flex items-center gap-1.5 shadow-xs" title="Unrestricted full workspace visibility">
                        <span class="size-1.5 rounded-full bg-amber-500"></span>
                        👑 {{ $userLabel }} — Full Access
                    </span>
                @elseif($userTier === 'lead')
                    <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full bg-sky-50 dark:bg-sky-950/80 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800 flex items-center gap-1.5 shadow-xs" title="Access to own and team members' records">
                        <span class="size-1.5 rounded-full bg-sky-500"></span>
                        🛡️ {{ $userLabel }} — Team Scope
                    </span>
                @else
                    <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5 shadow-xs" title="Strictly personal assigned and created tasks only">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                        🔒 {{ $userLabel }} — Personal Scope
                    </span>
                @endif

                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                    gemini-3.6-flash
                </span>
            </div>
        </div>

        <!-- Messages Area (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 max-w-4xl w-full mx-auto" x-data="{ scrollToBottom() { this.$el.scrollTop = this.$el.scrollHeight } }" x-init="scrollToBottom()">
            
            @if($messages->isEmpty())
                <!-- Welcome Banner & Prompt Starters for New Chat -->
                <div class="py-8 space-y-6 text-center">
                    <div class="size-16 mx-auto rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">How can I assist you with your tasks today?</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-md mx-auto mt-1">
                            @if($userTier === 'director')
                                As Director / Executive, you have full visibility across all teams, admission/ORIC/LMS projects, and member workloads.
                            @elseif($userTier === 'lead')
                                As Team Lead, you have visibility over your team's assigned projects, supervised members, and sprint deliverables.
                            @else
                                Under Role-Based Access Control, your AI consultation is strictly scoped to your assigned tasks and personal deliverables.
                            @endif
                        </p>
                    </div>

                    <!-- Prompt Starters Based on Role Hierarchy -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-2xl mx-auto text-left pt-2">
                        @if($userTier === 'director')
                            <button 
                                wire:click="askQuickQuery('Who is working on the Admission Project and what are their tasks?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">🎓 Admission Project Status</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"Who is working on the Admission Project and what are their tasks?"</p>
                            </button>

                            <button 
                                wire:click="askQuickQuery('What tasks are currently blocked across the workspace and why?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">🛑 Active Blockers Review</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"What tasks are currently blocked across the workspace and why?"</p>
                            </button>

                            <button 
                                wire:click="askQuickQuery('Show me Ubaid ur Rehman\'s QA tasks and their status')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">🧪 QA Tasks Overview</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"Show me Ubaid ur Rehman's QA tasks and their status"</p>
                            </button>

                            <button 
                                wire:click="askQuickQuery('What urgent tasks are due in the next 3 days?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">⚡ Urgent Deadlines</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"What urgent tasks are due in the next 3 days?"</p>
                            </button>
                        @elseif($userTier === 'lead')
                            <button 
                                wire:click="askQuickQuery('What tasks are my team members currently working on?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">👥 Team Members Workload</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"What tasks are my team members currently working on?"</p>
                            </button>

                            <button 
                                wire:click="askQuickQuery('What blocked tasks are in our team right now?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">🛑 Team Blockers</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"What blocked tasks are in our team right now?"</p>
                            </button>

                            <button 
                                wire:click="askQuickQuery('What urgent tasks are due in our team this week?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">⚡ Team Deadlines</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"What urgent tasks are due in our team this week?"</p>
                            </button>

                            <button 
                                wire:click="askQuickQuery('Summarize our team sprint progress and completed milestones')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">📊 Sprint Velocity</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"Summarize our team sprint progress and completed milestones"</p>
                            </button>
                        @else
                            <button 
                                wire:click="askQuickQuery('What tasks are currently assigned to me and what are their statuses?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">📋 My Assigned Tasks</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"What tasks are currently assigned to me and what are their statuses?"</p>
                            </button>

                            <button 
                                wire:click="askQuickQuery('What urgent tasks or upcoming deadlines do I need to prioritize?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">⚡ My Urgent Deadlines</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"What urgent tasks or upcoming deadlines do I need to prioritize?"</p>
                            </button>

                            <button 
                                wire:click="askQuickQuery('Are any of my assigned tasks currently marked as blocked?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">🛑 My Blockers</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"Are any of my assigned tasks currently marked as blocked?"</p>
                            </button>

                            <button 
                                wire:click="askQuickQuery('What is Director Khubaib Ahmed working on?')" 
                                class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 hover:border-indigo-500/50 hover:bg-white dark:hover:bg-zinc-800/80 transition-all text-xs space-y-1 group"
                            >
                                <span class="font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600">🔒 Role Hierarchy Test</span>
                                <p class="text-[11px] text-zinc-500 line-clamp-2">"What is Director Khubaib Ahmed working on? (Tests privacy boundary)"</p>
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <!-- Render Saved Chat Messages -->
                @foreach($messages as $msg)
                    @if($msg->role === 'user')
                        <div class="flex justify-end">
                            <div class="max-w-xl rounded-2xl bg-indigo-600 text-white px-4 py-3 text-xs shadow-sm">
                                <p class="leading-relaxed">{{ $msg->content }}</p>
                                <span class="text-[9px] text-indigo-200 mt-1 block text-right">{{ $msg->created_at->format('g:i A') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-3">
                            <div class="size-7 rounded-lg bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <div class="max-w-2xl rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 p-4 text-xs text-zinc-800 dark:text-zinc-200 shadow-xs prose dark:prose-invert leading-relaxed">
                                {!! Str::markdown($msg->content) !!}
                                <span class="text-[9px] text-zinc-400 mt-2 block not-prose">{{ $msg->created_at->format('g:i A') }}</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

            <!-- Live Thinking State -->
            @if($isThinking)
                <div class="flex items-center gap-3">
                    <div class="size-7 rounded-lg bg-purple-600 text-white flex items-center justify-center shrink-0 animate-pulse">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 px-4 py-3 text-xs text-zinc-500 flex items-center gap-2">
                        <span class="animate-spin size-3.5 border-2 border-indigo-500 border-t-transparent rounded-full"></span>
                        <span>Querying workspace records and consulting Gemini 3.6 Flash...</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Query Input Bar -->
        <div class="p-4 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 shrink-0">
            <form wire:submit.prevent="submitQuery" class="max-w-4xl mx-auto flex items-center gap-3">
                <input 
                    type="text" 
                    wire:model="userQuery" 
                    placeholder="Ask anything about tasks, spaces, blockers, or team members..." 
                    class="flex-1 text-xs rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 px-4 py-3 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-hidden"
                />
                <button 
                    type="submit" 
                    class="px-5 py-3 rounded-2xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-all cursor-pointer disabled:opacity-50 shrink-0"
                    wire:loading.attr="disabled"
                >
                    <span>Send Query</span>
                </button>
            </form>
        </div>

    </div>

</div>
