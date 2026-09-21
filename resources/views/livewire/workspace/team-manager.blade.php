<div class="space-y-8 pb-12">
    
    <!-- Top Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-5">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white flex items-center gap-2">
                <span>Teams & People</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                    {{ $members->count() }} members
                </span>
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Manage cross-functional teams, workspace invitations, and role-based permissions.</p>
        </div>

        <div class="flex items-center gap-3">
            @if($canManage)
                <button 
                    wire:click="$set('showTeamModal', true)" 
                    type="button" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 transition-colors cursor-pointer"
                >
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>New Team</span>
                </button>
            @endif

            @if($canInvite)
                <button 
                    wire:click="$set('showInviteModal', true)" 
                    type="button" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-all cursor-pointer"
                >
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    <span>Invite Member</span>
                </button>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-zinc-100 dark:bg-zinc-800/80 text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                    <svg class="size-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Member View (Read Only)</span>
                </span>
            @endif
        </div>
    </div>

    @if($inviteSuccessMessage)
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between">
            <span>{{ $inviteSuccessMessage }}</span>
            <button wire:click="$set('inviteSuccessMessage', null)" class="text-xs font-bold">&times;</button>
        </div>
    @endif

    <!-- 1. TEAMS SECTION -->
    <div class="space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Teams in {{ $workspace->name }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($teams as $team)
                <div class="p-5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="size-3 rounded-full" style="background-color: {{ $team->color }}"></span>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">{{ $team->name }}</h3>
                        </div>
                        @if($canManage)
                            <button wire:confirm="Delete this team?" wire:click="deleteTeam({{ $team->id }})" class="text-zinc-400 hover:text-red-500 text-xs cursor-pointer" title="Delete team">&times;</button>
                        @endif
                    </div>

                    @if($team->description)
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">{{ $team->description }}</p>
                    @endif

                    <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
                        <span>{{ $team->members->count() }} members</span>
                        <div class="flex -space-x-1.5 overflow-hidden">
                            @foreach($team->members->take(4) as $tm)
                                <img src="{{ $tm->avatar() }}" class="size-6 rounded-full ring-2 ring-white dark:ring-zinc-900" title="{{ $tm->name }}" alt="" />
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-8 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 text-center text-xs text-zinc-400">
                    No teams created yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2. WORKSPACE MEMBERS TABLE -->
    <div class="space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Members & Roles</h2>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-100 dark:border-zinc-800 text-zinc-400 uppercase text-[10px] tracking-wider bg-zinc-50/50 dark:bg-zinc-800/40">
                            <th class="py-3 px-5 font-medium">User</th>
                            <th class="py-3 px-4 font-medium">Job Title</th>
                            <th class="py-3 px-4 font-medium">Time Zone</th>
                            <th class="py-3 px-4 font-medium">Workspace Role</th>
                            <th class="py-3 px-4 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @foreach($members as $m)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $m->avatar() }}" class="size-8 rounded-full" alt="" />
                                        <div>
                                            <p class="font-bold text-zinc-900 dark:text-white">{{ $m->name }}</p>
                                            <p class="text-[11px] text-zinc-400">{{ $m->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-zinc-600 dark:text-zinc-300">
                                    {{ $m->pivot->job_title ?? $m->job_title ?? 'Team Member' }}
                                </td>

                                <td class="py-3.5 px-4 text-zinc-500 dark:text-zinc-400">
                                    {{ $m->pivot->timezone ?? $m->timezone ?? 'UTC' }}
                                </td>

                                <td class="py-3.5 px-4">
                                    @if($m->id === $workspace->owner_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                            Owner
                                        </span>
                                    @elseif(auth()->user()->canUpdateWorkspaceMemberRole($workspace, $m->id))
                                        <select 
                                            wire:change="updateMemberRole({{ $m->id }}, $event.target.value)" 
                                            class="text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 py-1 px-2.5 cursor-pointer"
                                        >
                                            <option value="admin" {{ $m->pivot->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="member" {{ $m->pivot->role === 'member' ? 'selected' : '' }}>Member</option>
                                            <option value="guest" {{ $m->pivot->role === 'guest' ? 'selected' : '' }}>Guest (View only)</option>
                                        </select>
                                    @else
                                        @if($m->pivot->role === 'admin')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                                Admin
                                            </span>
                                        @elseif($m->pivot->role === 'guest')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                                Guest
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                Member
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-right">
                                    @if(auth()->user()->canRemoveWorkspaceMember($workspace, $m->id))
                                        <button 
                                            wire:confirm="Remove {{ $m->name }} from this workspace?"
                                            wire:click="removeMember({{ $m->id }})" 
                                            class="text-xs text-red-500 hover:text-red-700 hover:underline cursor-pointer"
                                        >
                                            Remove
                                        </button>
                                    @else
                                        <span class="text-zinc-400 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. PENDING INVITATIONS -->
    @if($invites->count() > 0)
        <div class="space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Pending Invitations</h2>

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 divide-y divide-zinc-100 dark:divide-zinc-800 shadow-xs">
                @foreach($invites as $inv)
                    <div class="p-4 flex items-center justify-between text-xs">
                        <div>
                            <p class="font-semibold text-zinc-900 dark:text-white">{{ $inv->email }}</p>
                            <p class="text-[11px] text-zinc-400">Role: {{ ucfirst($inv->role) }} • Expires: {{ $inv->expires_at?->format('M j, Y') }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-950 text-amber-600 border border-amber-200">
                            Pending Invite
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Invite Member Modal -->
    @if($showInviteModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 space-y-4 animate-in zoom-in-95 duration-150">
                <h3 class="text-base font-bold text-zinc-900 dark:text-white">Invite Member to {{ $workspace->name }}</h3>

                <form wire:submit.prevent="inviteMember" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email Address</label>
                        <input 
                            type="email" 
                            wire:model="inviteEmail" 
                            required 
                            placeholder="colleague@company.com" 
                            class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-zinc-900 dark:text-white focus:ring-indigo-500"
                        />
                        @error('inviteEmail') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Role</label>
                        <select wire:model="inviteRole" class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-2 px-3">
                            <option value="admin">Admin (Can manage spaces, lists, and invite members)</option>
                            <option value="member">Member (Can create, edit, and complete tasks)</option>
                            <option value="guest">Guest (View-only access)</option>
                        </select>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button wire:click="$set('showInviteModal', false)" type="button" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">Send Invitation</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Create Team Modal -->
    @if($showTeamModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 space-y-4 animate-in zoom-in-95 duration-150">
                <h3 class="text-base font-bold text-zinc-900 dark:text-white">Create New Team</h3>

                <form wire:submit.prevent="createTeam" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Team Name</label>
                        <input 
                            type="text" 
                            wire:model="newTeamName" 
                            required 
                            placeholder="e.g. Mobile Engineering" 
                            class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-zinc-900 dark:text-white focus:ring-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Description</label>
                        <textarea 
                            wire:model="newTeamDescription" 
                            rows="2" 
                            placeholder="Team mission or scope..." 
                            class="w-full text-xs rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-zinc-900 dark:text-white focus:ring-indigo-500"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model="newTeamColor" class="size-8 rounded border-0 cursor-pointer" />
                            <span class="text-xs text-zinc-500">{{ $newTeamColor }}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button wire:click="$set('showTeamModal', false)" type="button" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">Save Team</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
