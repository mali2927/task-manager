<div class="relative" x-data="{ open: @entangle('isOpen') }" @click.outside="open = false" wire:poll.30s>
    <button 
        @click="open = !open" 
        type="button" 
        class="relative flex items-center justify-center p-2 rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors focus:outline-hidden"
        title="Notifications"
    >
        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex size-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-zinc-900">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100" 
        x-transition:enter-start="transform opacity-0 scale-95" 
        x-transition:enter-end="transform opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-75" 
        x-transition:leave-start="transform opacity-100 scale-100" 
        x-transition:leave-end="transform opacity-0 scale-95" 
        class="absolute right-0 mt-2 w-80 sm:w-96 origin-top-right rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xl shadow-zinc-950/10 z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-zinc-900 dark:text-white">Notifications</span>
                @if($unreadCount > 0)
                    <span class="rounded-full bg-indigo-50 dark:bg-indigo-950/50 px-2 py-0.5 text-xs font-medium text-indigo-600 dark:text-indigo-400">
                        {{ $unreadCount }} new
                    </span>
                @endif
            </div>

            @if($unreadCount > 0)
                <button 
                    wire:click="markAllAsRead" 
                    type="button" 
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                    Mark all read
                </button>
            @endif
        </div>

        <div class="max-h-[380px] overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800/60">
            @forelse($notifications as $item)
                <div 
                    wire:click="markAsRead({{ $item->id }})" 
                    class="flex items-start gap-3 p-3.5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 cursor-pointer transition-colors {{ $item->read_at ? 'opacity-65' : 'bg-indigo-50/30 dark:bg-indigo-950/20' }}"
                >
                    <div class="mt-0.5 shrink-0">
                        @if($item->type === 'assigned')
                            <div class="size-7 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                        @elseif($item->type === 'due_soon')
                            <div class="size-7 rounded-full bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        @elseif($item->type === 'status_changed')
                            <div class="size-7 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                        @else
                            <div class="size-7 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-zinc-900 dark:text-white truncate">{{ $item->title }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2 mt-0.5">{{ $item->message }}</p>
                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1 block">{{ $item->created_at->diffForHumans() }}</span>
                    </div>

                    @if(!$item->read_at)
                        <span class="size-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></span>
                    @endif
                </div>
            @empty
                <div class="py-8 text-center text-xs text-zinc-500 dark:text-zinc-400">
                    <p>You're all caught up!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
