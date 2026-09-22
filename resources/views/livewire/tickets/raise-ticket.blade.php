<div class="p-6 max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('workspace.tickets.my', ['workspace' => $workspace->slug]) }}" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs font-semibold flex items-center gap-1 transition-colors" wire:navigate>
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>My Tickets</span>
                </a>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">Raise a Support Ticket</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Submit an issue, bug, or service request. Our team will triage and resolve it according to our SLA commitments.
            </p>
        </div>
    </div>

    @if($isSubmitted)
        <div class="p-8 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center space-y-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="size-14 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center mx-auto border border-emerald-500/30">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 mb-2">
                    {{ $createdTicketNumber }}
                </span>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Ticket Submitted Successfully!</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-md mx-auto mt-1">
                    Your ticket has been logged and assigned an SLA timer. You will receive updates via email and in-app notifications as it progresses.
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 pt-2">
                <a 
                    href="{{ route('workspace.tickets.my', ['workspace' => $workspace->slug]) }}" 
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs transition-colors"
                    wire:navigate
                >
                    View in My Tickets
                </a>
                <button 
                    wire:click="resetForm" 
                    type="button" 
                    class="px-4 py-2 rounded-xl text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors cursor-pointer"
                >
                    Submit Another Ticket
                </button>
            </div>
        </div>
    @else
        <form wire:submit="submit" class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 shadow-xs space-y-6">
            <!-- Subject -->
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Ticket Subject <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    wire:model="subject" 
                    placeholder="Brief summary of the issue or request..." 
                    class="w-full text-sm px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-hidden focus:ring-1 focus:ring-indigo-500 transition-all font-medium"
                    required
                />
                @error('subject') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Project & Category Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Related Project -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Related Project <span class="text-zinc-400 font-normal">(Optional)</span>
                    </label>
                    <select 
                        wire:model="projectId"
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500"
                    >
                        <option value="">General / No Project</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}">
                                {{ $proj->name }} @if($proj->space)({{ $proj->space->name }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('projectId') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                        Category
                    </label>
                    <select 
                        wire:model="categoryId"
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500"
                    >
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Priority Selector with SLA -->
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Priority &amp; SLA
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach([
                        'urgent' => ['label' => 'Urgent', 'sla' => '4 hrs', 'color' => 'peer-checked:border-red-500 peer-checked:bg-red-500/10 peer-checked:text-red-500'],
                        'high' => ['label' => 'High', 'sla' => '24 hrs', 'color' => 'peer-checked:border-amber-500 peer-checked:bg-amber-500/10 peer-checked:text-amber-500'],
                        'normal' => ['label' => 'Normal', 'sla' => '3 days', 'color' => 'peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:text-blue-500'],
                        'low' => ['label' => 'Low', 'sla' => '5 days', 'color' => 'peer-checked:border-zinc-500 peer-checked:bg-zinc-500/10 peer-checked:text-zinc-400'],
                    ] as $val => $meta)
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="priority" value="{{ $val }}" class="sr-only peer" />
                            <div class="px-2 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 text-center transition-all hover:bg-zinc-50 dark:hover:bg-zinc-800/40 {{ $meta['color'] }}">
                                <div class="text-[11px] font-bold">{{ $meta['label'] }}</div>
                                <div class="text-[9px] text-zinc-400">{{ $meta['sla'] }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('priority') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Detailed Description <span class="text-red-500">*</span>
                </label>
                <textarea 
                    wire:model="description" 
                    rows="6" 
                    placeholder="Describe what happened, steps to reproduce, or specific requirements..." 
                    class="w-full text-xs px-3.5 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-hidden focus:ring-1 focus:ring-indigo-500 transition-all resize-y"
                    required
                ></textarea>
                @error('description') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Attachments Dropzone -->
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                    File Attachments (Optional, max 10MB each)
                </label>
                <div class="p-4 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/30 text-center">
                    <input 
                        type="file" 
                        wire:model="attachments" 
                        multiple 
                        class="text-xs text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 dark:file:bg-indigo-950/60 dark:file:text-indigo-400 hover:file:bg-indigo-100 cursor-pointer"
                    />
                    <div wire:loading wire:target="attachments" class="text-[11px] text-indigo-500 mt-1">
                        Uploading files...
                    </div>
                </div>
                @error('attachments.*') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <a 
                    href="{{ route('workspace.tickets.my', ['workspace' => $workspace->slug]) }}" 
                    class="px-4 py-2 text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
                    wire:navigate
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition-all cursor-pointer disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="submit">Submit Support Ticket</span>
                    <span wire:loading wire:target="submit" class="animate-spin size-4 border-2 border-white border-t-transparent rounded-full"></span>
                    <span wire:loading wire:target="submit">Submitting...</span>
                </button>
            </div>
        </form>
    @endif
</div>
