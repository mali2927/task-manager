<div class="w-full">
    <flux:dropdown position="bottom" align="start" class="w-full">
        <div class="p-1 rounded-2xl bg-white/80 dark:bg-zinc-900/80 border border-slate-200/70 dark:border-zinc-800/80 shadow-2xs backdrop-blur-xs hover:border-indigo-300 dark:hover:border-zinc-700 transition-all">
            <flux:sidebar.profile
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down"
                data-test="sidebar-menu-button"
                class="w-full !p-1.5 rounded-xl hover:!bg-transparent cursor-pointer"
            />
        </div>

        <flux:menu class="min-w-56 rounded-2xl p-2 shadow-xl border border-slate-200/80 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md">
            <div class="flex items-center gap-2.5 px-2 py-2 text-start rounded-xl bg-slate-50 dark:bg-zinc-800/60 border border-slate-200/60 dark:border-zinc-700/60 mb-2">
                <div class="relative shrink-0">
                    <flux:avatar
                        :name="auth()->user()->name"
                        :initials="auth()->user()->initials()"
                    />
                    <span class="absolute -bottom-0.5 -right-0.5 size-2 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></span>
                </div>
                <div class="grid flex-1 text-start text-xs leading-tight min-w-0">
                    <span class="font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</span>
                    <span class="text-[11px] text-slate-500 dark:text-zinc-400 truncate">{{ auth()->user()->email }}</span>
                </div>
            </div>
            <flux:menu.separator />
            <flux:menu.radio.group>
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate class="rounded-xl text-xs font-semibold">
                    {{ __('Settings') }}
                </flux:menu.item>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40"
                        data-test="logout-button"
                    >
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
</div>
