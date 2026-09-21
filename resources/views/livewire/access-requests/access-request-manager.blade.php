<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white">Access Requests &amp; Approvals</h1>
                @if($pendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                        {{ $pendingCount }} Pending
                    </span>
                @endif
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Manage incoming user signup requests. Approve users to create accounts and dispatch secure access links.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button 
                wire:click="$set('showAddUserModal', true)"
                type="button" 
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs transition-colors cursor-pointer"
            >
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add User Directly</span>
            </button>
        </div>
    </div>

    @if($feedbackMessage)
        <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ $feedbackMessage }}</span>
            </div>
            <button wire:click="$set('feedbackMessage', null)" class="text-xs font-bold hover:underline">Dismiss</button>
        </div>
    @endif

    <!-- Controls Row: Tabs & Search -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-zinc-50 dark:bg-zinc-900/60 p-2.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800">
        <!-- Tabs -->
        <div class="flex items-center gap-1 w-full sm:w-auto">
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All Requests'] as $k => $label)
                <button 
                    wire:click="$set('tab', '{{ $k }}')"
                    type="button" 
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all cursor-pointer {{ $tab === $k ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-xs font-semibold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' }}"
                >
                    {{ $label }}
                    @if($k === 'pending' && $pendingCount > 0)
                        <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-amber-500/20 text-amber-500">{{ $pendingCount }}</span>
                    @endif
                </button>
            @endforeach
        </div>

        <!-- Search input -->
        <div class="w-full sm:w-64">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search requests..." 
                class="w-full text-xs px-3 py-1.5 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-hidden focus:ring-1 focus:ring-indigo-500"
            />
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50 dark:bg-zinc-800/60 text-zinc-700 dark:text-zinc-300 font-semibold border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-5 py-3">Applicant</th>
                        <th class="px-4 py-3">Department &amp; Reason</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Submitted</th>
                        <th class="px-4 py-3">Reviewed By</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($requests as $req)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="size-8 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-500 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ substr($req->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-zinc-900 dark:text-white truncate">{{ $req->name }}</div>
                                        <div class="text-[11px] text-zinc-500 truncate">{{ $req->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 max-w-xs">
                                <div class="font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $req->department ?: 'General Access' }}</div>
                                <div class="text-[11px] text-zinc-500 line-clamp-2 mt-0.5">{{ $req->reason }}</div>
                                @if($req->status === 'rejected' && $req->rejection_reason)
                                    <div class="text-[10px] text-rose-500 mt-1 italic">Reason: {{ $req->rejection_reason }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($req->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                        <span class="size-1.5 rounded-full bg-amber-500"></span> Pending
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span> Approved ({{ ucfirst($req->assigned_role) }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-500 border border-rose-500/20">
                                        <span class="size-1.5 rounded-full bg-rose-500"></span> Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-zinc-500 whitespace-nowrap">
                                {{ $req->created_at->format('M d, Y') }}
                                <div class="text-[10px] text-zinc-400">{{ $req->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-zinc-500 whitespace-nowrap">
                                @if($req->reviewedBy)
                                    <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ $req->reviewedBy->name }}</div>
                                    <div class="text-[10px] text-zinc-400">{{ $req->reviewed_at?->format('M d, H:i') }}</div>
                                @else
                                    <span class="text-zinc-400 italic">Not reviewed</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                @if($req->status === 'pending')
                                    <div class="inline-flex items-center gap-1.5">
                                        <button 
                                            wire:click="openApproveModal({{ $req->id }})"
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 transition-colors cursor-pointer"
                                        >
                                            Approve
                                        </button>
                                        <button 
                                            wire:click="openRejectModal({{ $req->id }})"
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 transition-colors cursor-pointer"
                                        >
                                            Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-zinc-400 text-xs">Archived</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-zinc-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="size-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium">No access requests found matching your filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Approve Modal -->
    @if($showApproveModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Approve Access Request</h3>
                    <button wire:click="$set('showApproveModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Approving this request will provision an account and send a secure invitation email with instructions to set their password.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Assign Workspace Role</label>
                    <select 
                        wire:model="assignRole"
                        class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                    >
                        <option value="member">Member (Standard contributor, task &amp; ticket workflows)</option>
                        <option value="guest">Guest (View-only, raise personal tickets)</option>
                        <option value="admin">Admin (Full workspace administration)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button 
                        wire:click="$set('showApproveModal', false)"
                        class="px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="approveRequest"
                        class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs cursor-pointer"
                    >
                        Confirm &amp; Send Invitation
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if($showRejectModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Reject Access Request</h3>
                    <button wire:click="$set('showRejectModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    The request will be archived as rejected. You may optionally provide a reason that will be included in the email notice.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Reason for Rejection (Optional)</label>
                    <textarea 
                        wire:model="rejectionReason"
                        rows="3"
                        placeholder="e.g. Please register using your institutional email address..."
                        class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 resize-none"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button 
                        wire:click="$set('showRejectModal', false)"
                        class="px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="rejectRequest"
                        class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-xs cursor-pointer"
                    >
                        Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Direct Add User Modal -->
    @if($showAddUserModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Provision User Directly</h3>
                    <button wire:click="$set('showAddUserModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Directly provision a new team member. A welcome email with a link to set their password will be sent automatically.
                </p>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Full Name</label>
                        <input 
                            type="text" 
                            wire:model="newUserName"
                            placeholder="e.g. Sara Khan"
                            class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                        />
                        @error('newUserName') <span class="text-[11px] text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Email Address</label>
                        <input 
                            type="email" 
                            wire:model="newUserEmail"
                            placeholder="sara.khan@stmu.edu.pk"
                            class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                        />
                        @error('newUserEmail') <span class="text-[11px] text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Job Title</label>
                        <input 
                            type="text" 
                            wire:model="newUserJobTitle"
                            placeholder="e.g. Senior Software Engineer"
                            class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Workspace Role</label>
                        <select 
                            wire:model="newUserRole"
                            class="w-full text-xs px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200"
                        >
                            <option value="member">Member</option>
                            <option value="guest">Guest</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button 
                        wire:click="$set('showAddUserModal', false)"
                        class="px-3 py-2 rounded-xl text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="createUserDirectly"
                        class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs cursor-pointer"
                    >
                        Create &amp; Send Setup Link
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
