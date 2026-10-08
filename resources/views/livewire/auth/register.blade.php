<x-layouts::auth :title="__('Register - STMU MIS')">
    <div class="flex flex-col gap-5">
        
        <!-- Header -->
        <div class="flex flex-col gap-1.5 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 self-center sm:self-start px-2.5 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-[11px] font-bold text-emerald-400 tracking-wide uppercase">
                <span class="size-1.5 rounded-full bg-emerald-400"></span>
                Faculty & Personnel Onboarding
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white mt-1">
                {{ __('Create Account') }}
            </h2>
            <p class="text-xs text-slate-400">
                {{ __('Register your credentials to collaborate across STMU workspaces and projects.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <!-- Role Guidance Notice -->
        <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 flex items-start gap-2.5 text-xs text-slate-300">
            <svg class="size-4 text-indigo-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="leading-relaxed text-[11px]">
                <strong class="text-slate-200">Institutional Notice:</strong> New accounts are assigned standard Member privileges in the default STMU MIS Workspace. Elevated privileges (Lead, Director) are assigned by system administrators.
            </div>
        </div>

        <!-- Register Form -->
        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-4">
            @csrf

            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Full Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('e.g. Dr. Jane Doe / Muhammad Ali')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Official University Email')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="name@stmu.edu.pk"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Create a strong password (min 8 characters)')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Re-enter your password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Submit Button -->
            <div class="pt-2">
                <flux:button 
                    type="submit" 
                    variant="primary" 
                    class="w-full bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 hover:from-emerald-500 hover:to-indigo-500 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all duration-200" 
                    data-test="register-user-button"
                >
                    <div class="flex items-center justify-center gap-2">
                        <span>{{ __('Register STMU MIS Account') }}</span>
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                </flux:button>
            </div>
        </form>

        <!-- Link to Login -->
        <div class="pt-2 border-t border-slate-800/80 text-center text-xs text-slate-400">
            <span>{{ __('Already registered with STMU MIS?') }}</span>
            <flux:link :href="route('login')" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1" wire:navigate>
                {{ __('Sign in to your account') }}
            </flux:link>
        </div>
    </div>
</x-layouts::auth>
