<div class="flex flex-col gap-5">
        <!-- Header -->
        <div class="flex flex-col gap-1.5 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 self-center sm:self-start px-2.5 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold">
                <span class="size-2 rounded-full bg-indigo-500 animate-pulse"></span>
                <span>Restricted Workspace Access</span>
            </div>
            <h1 class="text-xl font-bold text-white tracking-tight">Request Platform Access</h1>
            <p class="text-xs text-slate-400">
                STMU MIS is invite-only. Submit your application below for administrator review.
            </p>
        </div>

        @if($isSubmitted)
            <div class="p-6 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 text-center space-y-3 animate-in fade-in zoom-in-95 duration-200">
                <div class="size-12 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto border border-emerald-500/30">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white">Application Received!</h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Your access request for <strong class="text-white">{{ $email }}</strong> has been submitted. Our administrators will review your credentials and email you an invitation once approved.
                </p>
                <div class="pt-2">
                    <a href="{{ route('login') }}" class="inline-block px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-lg border border-slate-700 transition-colors" wire:navigate>
                        Back to Sign In
                    </a>
                </div>
            </div>
        @else
            <form wire:submit="submit" class="flex flex-col gap-4">
                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                    <input 
                        type="text" 
                        wire:model="name" 
                        placeholder="e.g. Dr. Muhammad Ali" 
                        class="w-full text-xs px-3 py-2.5 rounded-xl bg-slate-900/90 border border-slate-800 text-white placeholder-slate-500 focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        required
                    />
                    @error('name') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                    <input 
                        type="email" 
                        wire:model="email" 
                        placeholder="you@institution.edu" 
                        class="w-full text-xs px-3 py-2.5 rounded-xl bg-slate-900/90 border border-slate-800 text-white placeholder-slate-500 focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        required
                    />
                    @error('email') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Department / Organization -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Department / Organization</label>
                    <input 
                        type="text" 
                        wire:model="department" 
                        placeholder="e.g. MIS / Software Engineering" 
                        class="w-full text-xs px-3 py-2.5 rounded-xl bg-slate-900/90 border border-slate-800 text-white placeholder-slate-500 focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                    />
                    @error('department') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Target Workspace (if multiple) -->
                @if($workspaces->count() > 1)
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Target Workspace</label>
                        <select 
                            wire:model="workspaceId"
                            class="w-full text-xs px-3 py-2.5 rounded-xl bg-slate-900/90 border border-slate-800 text-white focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        >
                            @foreach($workspaces as $ws)
                                <option value="{{ $ws->id }}">{{ $ws->name }}</option>
                            @endforeach
                        </select>
                        @error('workspaceId') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Reason for Access -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Reason for Access / Role Intent</label>
                    <textarea 
                        wire:model="reason" 
                        rows="3" 
                        placeholder="Briefly explain your role and why you require access to the platform..." 
                        class="w-full text-xs px-3 py-2.5 rounded-xl bg-slate-900/90 border border-slate-800 text-white placeholder-slate-500 focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all resize-none"
                        required
                    ></textarea>
                    @error('reason') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-lg shadow-indigo-600/20 cursor-pointer disabled:opacity-50 flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove wire:target="submit">Submit Access Request</span>
                    <span wire:loading wire:target="submit" class="animate-spin size-4 border-2 border-white border-t-transparent rounded-full"></span>
                    <span wire:loading wire:target="submit">Submitting...</span>
                </button>
            </form>
        @endif

        <!-- Link to Login -->
        <div class="pt-2 border-t border-slate-800/80 text-center text-xs text-slate-400">
            <span>{{ __('Already have an approved account?') }}</span>
            <flux:link :href="route('login')" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1" wire:navigate>
                {{ __('Sign In') }}
            </flux:link>
        </div>
    </div>
