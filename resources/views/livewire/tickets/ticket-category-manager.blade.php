<div class="p-6 max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('workspace.tickets.queue', ['workspace' => $workspace->slug]) }}" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs font-semibold flex items-center gap-1 transition-colors" wire:navigate>
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Triage Queue</span>
                </a>
            </div>
            <h1 class="text-xl font-bold text-zinc-900 dark:text-white mt-1">Ticket Categories &amp; Routing</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Organize support requests and define default routing teams for automated assignment suggestion.
            </p>
        </div>

        <button 
            wire:click="openCreateModal"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs transition-colors cursor-pointer"
        >
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <span>Add Category</span>
        </button>
    </div>

    @if($feedbackMessage)
        <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs flex items-center justify-between">
            <span>{{ $feedbackMessage }}</span>
            <button wire:click="$set('feedbackMessage', null)" class="text-xs font-bold hover:underline cursor-pointer">Dismiss</button>
        </div>
    @endif

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($categories as $cat)
            <div class="p-5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm text-zinc-900 dark:text-white">{{ $cat->name }}</h3>
                        <span class="text-[11px] font-semibold text-zinc-400">
                            {{ $cat->tickets->count() }} tickets
                        </span>
                    </div>

                    <p class="text-xs text-zinc-500 line-clamp-2">
                        {{ $cat->description ?: 'No description provided.' }}
                    </p>
                </div>

                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-zinc-400 block font-medium">Default Team Routing:</span>
                        @if($cat->defaultTeam)
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-0.5">
                                <span class="size-2 rounded-full" style="background-color: {{ $cat->defaultTeam->color }}"></span>
                                <span>{{ $cat->defaultTeam->name }}</span>
                            </span>
                        @else
                            <span class="text-xs text-zinc-400 italic">None (Manual Triage)</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-1">
                        <button 
                            wire:click="openEditModal({{ $cat->id }})"
                            class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                            title="Edit Category"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button 
                            wire:confirm="Are you sure you want to delete this category?"
                            wire:click="deleteCategory({{ $cat->id }})"
                            class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors cursor-pointer"
                            title="Delete Category"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center text-zinc-500 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                <p class="font-medium text-sm">No ticket categories created yet.</p>
                <p class="text-xs text-zinc-400 mt-1">Create categories like "Bug Reports", "IT Support", or "Feature Requests" to route tickets automatically.</p>
            </div>
        @endforelse
    </div>

    <!-- Create / Edit Category Modal -->
    @if($showCategoryModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">
                        {{ $editingCategoryId ? 'Edit Category' : 'Create Ticket Category' }}
                    </h3>
                    <button wire:click="$set('showCategoryModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="name"
                            placeholder="e.g. IT &amp; Infrastructure Support"
                            class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                        />
                        @error('name') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Description</label>
                        <textarea 
                            wire:model="description"
                            rows="2"
                            placeholder="Brief guidance on what tickets belong in this category..."
                            class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 resize-none"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Default Routing Team</label>
                        <select 
                            wire:model="defaultTeamId"
                            class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                        >
                            <option value="">None (Manual Triage)</option>
                            @foreach($teams as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <button 
                        wire:click="$set('showCategoryModal', false)"
                        class="px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="saveCategory"
                        class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs cursor-pointer"
                    >
                        Save Category
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
