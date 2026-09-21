<div class="space-y-6 pb-12">
    
    <!-- Top Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white flex items-center gap-2">
                <span>My Assigned Tasks</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                    {{ $overdue->count() + $dueToday->count() + $upcoming->count() }} active
                </span>
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Your dedicated daily focus list across all spaces and projects.</p>
        </div>

        <!-- Gemini AI Daily Briefing Trigger -->
        <button 
            wire:click="generateAiDigest" 
            type="button" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-all cursor-pointer"
        >
            <span wire:loading.remove wire:target="generateAiDigest">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </span>
            <span wire:loading wire:target="generateAiDigest" class="animate-spin size-4 border-2 border-white border-t-transparent rounded-full"></span>
            <span>Generate Today's AI Briefing</span>
        </button>
    </div>

    <!-- AI Briefing Card -->
    @if($aiDigest)
        <div class="p-5 rounded-2xl border border-indigo-200 dark:border-indigo-900/70 bg-gradient-to-r from-indigo-50/80 to-purple-50/80 dark:from-indigo-950/40 dark:to-purple-950/40 shadow-sm relative space-y-2">
            <div class="flex items-center justify-between pb-2 border-b border-indigo-200/50 dark:border-indigo-800/50">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                    <span>Personal AI Productivity Briefing</span>
                </div>
                <button wire:click="$set('aiDigest', null)" class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">&times; Dismiss</button>
            </div>
            <div class="text-xs text-zinc-800 dark:text-zinc-200 prose dark:prose-invert max-w-none leading-relaxed">
                {!! Str::markdown($aiDigest) !!}
            </div>
        </div>
    @endif

    <!-- Tasks Sections -->
    <div class="space-y-6">
        
        <!-- 1. OVERDUE SECTION -->
        @if($overdue->count() > 0)
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                    <span class="size-2 rounded-full bg-rose-500 animate-ping"></span>
                    <span>Overdue ({{ $overdue->count() }})</span>
                </div>

                <div class="divide-y divide-zinc-200/80 dark:divide-zinc-800 rounded-2xl border border-rose-200 dark:border-rose-950/60 bg-rose-50/20 dark:bg-rose-950/10 overflow-hidden shadow-xs">
                    @foreach($overdue as $task)
                        @include('livewire.tasks.partials.my-task-row', ['task' => $task, 'isOverdue' => true])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 2. DUE TODAY SECTION -->
        <div class="space-y-3">
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                <span class="size-2 rounded-full bg-amber-500"></span>
                <span>Due Today ({{ $dueToday->count() }})</span>
            </div>

            <div class="divide-y divide-zinc-200/80 dark:divide-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-xs">
                @forelse($dueToday as $task)
                    @include('livewire.tasks.partials.my-task-row', ['task' => $task])
                @empty
                    <div class="p-6 text-center text-xs text-zinc-400">
                        No tasks due today.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. UPCOMING / NEXT SECTION -->
        <div class="space-y-3">
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                <span class="size-2 rounded-full bg-zinc-400"></span>
                <span>Upcoming ({{ $upcoming->count() }})</span>
            </div>

            <div class="divide-y divide-zinc-200/80 dark:divide-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-xs">
                @forelse($upcoming as $task)
                    @include('livewire.tasks.partials.my-task-row', ['task' => $task])
                @empty
                    <div class="p-6 text-center text-xs text-zinc-400">
                        No upcoming tasks scheduled.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4. COMPLETED SECTION -->
        @if($completed->count() > 0)
            <div class="space-y-3" x-data="{ showDone: false }">
                <div @click="showDone = !showDone" class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 cursor-pointer select-none">
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-emerald-500"></span>
                        <span>Completed ({{ $completed->count() }})</span>
                    </div>
                    <span class="text-[11px] text-zinc-400" x-text="showDone ? 'Hide' : 'Show'">Show</span>
                </div>

                <div x-show="showDone" class="divide-y divide-zinc-200/80 dark:divide-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-xs" style="display: none;">
                    @foreach($completed as $task)
                        @include('livewire.tasks.partials.my-task-row', ['task' => $task, 'isDone' => true])
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Embedded Task Detail Slide-Over Modal -->
    <livewire:tasks.task-detail-modal />
</div>
