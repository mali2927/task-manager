<x-layouts::auth :title="__('Sign In - STMU MIS')">
    <div class="flex flex-col gap-5" x-data="{
        roles: [
            { 
                id: 'director', 
                name: 'Director', 
                user: 'Khubaib Ahmed', 
                email: 'khubaib@example.com', 
                badge: 'Admin', 
                badgeColor: 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                note: 'Full institutional authority: All spaces, team management & unrestricted Gemini AI insights.' 
            },
            { 
                id: 'lead', 
                name: 'Team Lead', 
                user: 'Khurram Ahmed', 
                email: 'khurram@example.com', 
                badge: 'Lead', 
                badgeColor: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                note: 'Project leadership: Project planning, team assignment & departmental reports.' 
            },
            { 
                id: 'engineer', 
                name: 'Software Eng', 
                user: 'Muhammad Ali', 
                email: 'muhammad.ali@example.com', 
                badge: 'Dev', 
                badgeColor: 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
                note: 'Engineering execution: Code implementation, task tracking & sprint checklists.' 
            },
            { 
                id: 'qa', 
                name: 'QA Engineer', 
                user: 'Ubaid ur Rehman', 
                email: 'ubaid@example.com', 
                badge: 'QA', 
                badgeColor: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                note: 'Quality assurance: Bug verification, test reports & release validation.' 
            }
        ],
        selectedRole: null,
        selectRole(role) {
            this.selectedRole = role;
            const emailInput = document.getElementById('login-email') || document.querySelector('input[name=email]');
            const passInput = document.getElementById('login-password') || document.querySelector('input[name=password]');
            if (emailInput) {
                emailInput.value = role.email;
                emailInput.dispatchEvent(new Event('input', { bubbles: true }));
                emailInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (passInput) {
                passInput.value = 'password';
                passInput.dispatchEvent(new Event('input', { bubbles: true }));
                passInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }">
        
        <!-- Header -->
        <div class="flex flex-col gap-1.5 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 self-center sm:self-start px-2.5 py-0.5 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-[11px] font-bold text-indigo-300 tracking-wide uppercase">
                <span class="size-1.5 rounded-full bg-indigo-400"></span>
                STMU MIS Single Sign-On
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white mt-1">
                {{ __('Welcome Back') }}
            </h2>
            <p class="text-xs text-slate-400">
                {{ __('Sign in with your university credentials or choose a quick demo account.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <!-- Quick Demo Profiles Section -->
        <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <svg class="size-3.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Quick-Fill Demo Roles
                </span>
                <span class="text-[10px] text-slate-400 font-mono">pwd: password</span>
            </div>

            <!-- Role Pills Grid -->
            <div class="grid grid-cols-2 gap-1.5">
                <template x-for="r in roles" :key="r.id">
                    <button
                        type="button"
                        @click="selectRole(r)"
                        class="flex items-center justify-between p-2 rounded-lg border text-left transition-all text-xs group"
                        :class="selectedRole?.id === r.id 
                            ? 'border-indigo-500 bg-indigo-950/40 text-white ring-1 ring-indigo-500/50' 
                            : 'border-slate-800 bg-slate-900/60 text-slate-300 hover:border-slate-700 hover:bg-slate-800/60'"
                    >
                        <div class="truncate mr-1">
                            <span class="font-bold text-[11px] block truncate" x-text="r.name"></span>
                            <span class="text-[10px] text-slate-400 block truncate" x-text="r.user"></span>
                        </div>
                        <span 
                            class="text-[9px] font-extrabold px-1.5 py-0.5 rounded border shrink-0" 
                            :class="r.badgeColor"
                            x-text="r.badge"
                        ></span>
                    </button>
                </template>
            </div>

            <!-- Active Role Note Callout -->
            <div 
                x-show="selectedRole" 
                x-cloak 
                x-transition 
                class="mt-2.5 p-2 rounded-lg bg-indigo-950/30 border border-indigo-800/40 text-[11px] text-indigo-200 flex items-start gap-2"
            >
                <svg class="size-4 text-indigo-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-semibold text-white" x-text="selectedRole ? selectedRole.name + ' (' + selectedRole.user + '): ' : ''"></span>
                    <span x-text="selectedRole ? selectedRole.note : ''"></span>
                </div>
            </div>
        </div>

        <!-- Biometric / Passkey Login -->
        <x-passkey-verify />

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
            @csrf

            <!-- Email Address -->
            <div>
                <flux:input
                    id="login-email"
                    name="email"
                    :label="__('University Email Address')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="khubaib@example.com"
                />
            </div>

            <!-- Password -->
            <div class="relative">
                <flux:input
                    id="login-password"
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-xs end-0 text-indigo-400 hover:text-indigo-300" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <flux:checkbox name="remember" :label="__('Keep me signed in on this device')" :checked="old('remember')" />
            </div>

            <!-- Submit Button -->
            <div class="pt-1">
                <flux:button 
                    variant="primary" 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-indigo-600 via-indigo-500 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-indigo-600/25 transition-all duration-200" 
                    data-test="login-button"
                >
                    <div class="flex items-center justify-center gap-2">
                        <span>{{ __('Sign In to STMU MIS') }}</span>
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>
                </flux:button>
            </div>
        </form>

        <!-- Link to Register -->
        <div class="pt-2 border-t border-slate-800/80 text-center text-xs text-slate-400">
            <span>{{ __('New to STMU MIS?') }}</span>
            <flux:link :href="route('register')" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1" wire:navigate>
                {{ __('Create an account') }}
            </flux:link>
        </div>
    </div>
</x-layouts::auth>
