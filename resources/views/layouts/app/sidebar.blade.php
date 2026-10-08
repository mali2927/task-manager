<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-950 font-sans antialiased selection:bg-indigo-500 selection:text-white">
        
        <!-- Sidebar -->
        @php
            $isRequester = auth()->user()->isWorkspaceRequester($currentWorkspace);
        @endphp
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 w-64">
            
            <!-- Workspace Brand / Switcher Header -->
            <flux:sidebar.header class="pb-3 border-b border-zinc-200/80 dark:border-zinc-800/80 mb-2">
                <div class="flex items-center gap-3 w-full">
                    <img src="/stmu-logo.png" alt="STMU MIS" class="size-9 object-contain rounded-lg p-0.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-xs shrink-0" />

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <h2 class="text-xs font-black text-zinc-900 dark:text-white truncate tracking-tight">
                                STMU MIS
                            </h2>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                {{ $isRequester ? 'Requester' : 'Portal' }}
                            </span>
                        </div>
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block truncate font-medium">
                            {{ $currentWorkspace?->name ?? 'Task Manager' }}
                        </span>
                    </div>

                    <flux:sidebar.collapse class="lg:hidden" />
                </div>
            </flux:sidebar.header>

            <!-- Main Navigation Group -->
            <flux:sidebar.nav>
                @if(!$isRequester)
                    <flux:sidebar.group :heading="__('Workspace')" class="grid gap-0.5">
                        <!-- Dashboard -->
                        <flux:sidebar.item 
                            icon="layout-grid" 
                            :href="route('dashboard')" 
                            :current="request()->routeIs('dashboard')" 
                            wire:navigate
                        >
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>

                        <!-- My Tasks -->
                        <flux:sidebar.item 
                            icon="clipboard-document-check" 
                            :href="route('my-tasks')" 
                            :current="request()->routeIs('my-tasks')" 
                            wire:navigate
                        >
                            {{ __('My Tasks') }}
                        </flux:sidebar.item>

                        <!-- All Tasks (Board / List / Gantt) -->
                        @if($currentWorkspace)
                            <flux:sidebar.item 
                                icon="squares-2x2" 
                                :href="route('workspace.tasks', ['workspace' => $currentWorkspace->slug])" 
                                :current="request()->routeIs('workspace.tasks')" 
                                wire:navigate
                            >
                                {{ __('Task Board') }}
                            </flux:sidebar.item>

                            <!-- AI Assistant -->
                            <flux:sidebar.item 
                                icon="sparkles" 
                                :href="route('workspace.ai', ['workspace' => $currentWorkspace->slug])" 
                                :current="request()->routeIs('workspace.ai')" 
                                wire:navigate
                                class="group relative"
                            >
                                <span class="flex items-center justify-between w-full">
                                    <span>{{ __('AI Assistant') }}</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-600 dark:text-purple-400">Gemini</span>
                                </span>
                            </flux:sidebar.item>

                            <!-- Teams & Members -->
                            <flux:sidebar.item 
                                icon="user-group" 
                                :href="route('workspace.teams', ['workspace' => $currentWorkspace->slug])" 
                                :current="request()->routeIs('workspace.teams')" 
                                wire:navigate
                            >
                                {{ __('Teams & People') }}
                            </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif

                <!-- Support & Helpdesk -->
                @if($currentWorkspace)
                    <flux:sidebar.group :heading="$isRequester ? __('Support Tickets') : __('Support & Helpdesk')" class="grid gap-0.5 {{ $isRequester ? '' : 'mt-3' }}">
                        <!-- My Tickets -->
                        <flux:sidebar.item 
                            icon="ticket" 
                            :href="route('workspace.tickets.my', ['workspace' => $currentWorkspace->slug])" 
                            :current="request()->routeIs('workspace.tickets.my')" 
                            wire:navigate
                        >
                            {{ __('My Tickets') }}
                        </flux:sidebar.item>

                        <!-- Raise Ticket -->
                        <flux:sidebar.item 
                            icon="plus-circle" 
                            :href="route('workspace.tickets.raise', ['workspace' => $currentWorkspace->slug])" 
                            :current="request()->routeIs('workspace.tickets.raise')" 
                            wire:navigate
                        >
                            {{ __('Raise Ticket') }}
                        </flux:sidebar.item>

                        @if(!$isRequester && auth()->user()->isWorkspaceAdmin($currentWorkspace))
                            <!-- Triage Queue -->
                            <flux:sidebar.item 
                                icon="inbox-stack" 
                                :href="route('workspace.tickets.queue', ['workspace' => $currentWorkspace->slug])" 
                                :current="request()->routeIs('workspace.tickets.queue')" 
                                wire:navigate
                            >
                                <div class="flex items-center justify-between w-full">
                                    <span>{{ __('Triage Queue') }}</span>
                                    @php
                                        $openTicketsCount = \App\Models\Ticket::where('workspace_id', $currentWorkspace->id)->where('status', 'open')->count();
                                    @endphp
                                    @if($openTicketsCount > 0)
                                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full bg-indigo-500/20 text-indigo-500">
                                            {{ $openTicketsCount }}
                                        </span>
                                    @endif
                                </div>
                            </flux:sidebar.item>

                            <!-- Team Capacity -->
                            <flux:sidebar.item 
                                icon="chart-bar" 
                                :href="route('workspace.tickets.capacity', ['workspace' => $currentWorkspace->slug])" 
                                :current="request()->routeIs('workspace.tickets.capacity')" 
                                wire:navigate
                            >
                                {{ __('Team Capacity') }}
                            </flux:sidebar.item>

                            <!-- Categories & Routing -->
                            <flux:sidebar.item 
                                icon="tag" 
                                :href="route('workspace.tickets.categories', ['workspace' => $currentWorkspace->slug])" 
                                :current="request()->routeIs('workspace.tickets.categories')" 
                                wire:navigate
                            >
                                {{ __('Categories & Routing') }}
                            </flux:sidebar.item>

                            <!-- Access Requests -->
                            <flux:sidebar.item 
                                icon="user-plus" 
                                :href="route('workspace.access-requests', ['workspace' => $currentWorkspace->slug])" 
                                :current="request()->routeIs('workspace.access-requests')" 
                                wire:navigate
                            >
                                <div class="flex items-center justify-between w-full">
                                    <span>{{ __('Access Requests') }}</span>
                                    @php
                                        $pendingAccessCount = \App\Models\AccessRequest::where(fn ($q) => $q->where('workspace_id', $currentWorkspace->id)->orWhereNull('workspace_id'))->where('status', 'pending')->count();
                                    @endphp
                                    @if($pendingAccessCount > 0)
                                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full bg-amber-500/20 text-amber-500">
                                            {{ $pendingAccessCount }}
                                        </span>
                                    @endif
                                </div>
                            </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif

                <!-- Spaces Hierarchy Tree -->
                @if(!$isRequester && isset($workspaceSpaces) && $workspaceSpaces->count() > 0)
                    <flux:sidebar.group :heading="__('Spaces & Folders')" class="grid gap-0.5 mt-4">
                        @foreach($workspaceSpaces as $sp)
                            <flux:sidebar.item 
                                icon="folder" 
                                :href="route('workspace.tasks', ['workspace' => $currentWorkspace->slug, 'space' => $sp->id])"
                                :current="request()->fullUrlIs('*space=' . $sp->id . '*')"
                                wire:navigate
                            >
                                <div class="flex items-center justify-between w-full">
                                    <span class="truncate flex items-center gap-1.5">
                                        <span class="size-2 rounded-full shrink-0" style="background-color: {{ $sp->color }}"></span>
                                        <span class="truncate">{{ $sp->name }}</span>
                                    </span>
                                    <span class="text-[10px] text-zinc-400">{{ $sp->projects->count() }}</span>
                                </div>
                            </flux:sidebar.item>
                        @endforeach
                    </flux:sidebar.group>
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
