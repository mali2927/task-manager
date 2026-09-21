<?php

namespace App\Livewire\AccessRequests;

use App\Mail\AccessRequestApprovedMail;
use App\Mail\AccessRequestRejectedMail;
use App\Models\AccessRequest;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AccessRequestManager extends Component
{
    use WithPagination;

    public Workspace $workspace;
    public string $tab = 'pending'; // pending, approved, rejected, all
    public string $search = '';

    // Approval Modal State
    public bool $showApproveModal = false;
    public ?int $selectedRequestId = null;
    public string $assignRole = 'member'; // member, guest, admin

    // Rejection Modal State
    public bool $showRejectModal = false;
    public string $rejectionReason = '';

    // Direct "Add User" Modal State
    public bool $showAddUserModal = false;
    public string $newUserName = '';
    public string $newUserEmail = '';
    public string $newUserRole = 'member';
    public string $newUserJobTitle = '';

    // Flash message
    public ?string $feedbackMessage = null;

    public function mount(Workspace $workspace): void
    {
        $this->workspace = $workspace;
        abort_unless(Auth::user()->isWorkspaceAdmin($this->workspace), 403, 'Unauthorized. Access restricted to workspace administrators.');
    }

    public function openApproveModal(int $id): void
    {
        $this->selectedRequestId = $id;
        $req = AccessRequest::find($id);
        $this->assignRole = $req?->assigned_role ?: 'member';
        $this->showApproveModal = true;
    }

    public function approveRequest(): void
    {
        abort_unless(Auth::user()->isWorkspaceAdmin($this->workspace), 403);

        $request = AccessRequest::findOrFail($this->selectedRequestId);

        // Check if user already exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Create the new User
            $randomPassword = Str::random(32);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($randomPassword),
                'job_title' => $request->department ?: 'Team Member',
                'timezone' => 'UTC',
            ]);
        }

        // Attach user to workspace with chosen role
        if (!$this->workspace->members()->where('users.id', $user->id)->exists()) {
            $this->workspace->members()->attach($user->id, [
                'role' => $this->assignRole,
                'job_title' => $user->job_title,
                'timezone' => $user->timezone,
            ]);
        } else {
            $this->workspace->members()->updateExistingPivot($user->id, [
                'role' => $this->assignRole,
            ]);
        }

        // Sync Spatie role if defined
        $spatieRoleName = match ($this->assignRole) {
            'admin' => 'Admin',
            'guest' => 'Guest',
            default => 'Member',
        };
        if (\Spatie\Permission\Models\Role::where('name', $spatieRoleName)->exists()) {
            $user->syncRoles([$spatieRoleName]);
        }

        // Generate password reset token
        $token = Password::broker()->createToken($user);
        $passwordResetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        // Send approval notification email
        try {
            Mail::to($user->email)->queue(new AccessRequestApprovedMail($user, $passwordResetUrl, $this->assignRole));
        } catch (\Throwable $e) {
            report($e);
        }

        // Update Access Request
        $request->update([
            'status' => 'approved',
            'assigned_role' => $this->assignRole,
            'reviewed_by_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->showApproveModal = false;
        $this->selectedRequestId = null;
        $this->feedbackMessage = "Access approved for {$user->name}. Invitation email dispatched.";
    }

    public function openRejectModal(int $id): void
    {
        $this->selectedRequestId = $id;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function rejectRequest(): void
    {
        abort_unless(Auth::user()->isWorkspaceAdmin($this->workspace), 403);

        $request = AccessRequest::findOrFail($this->selectedRequestId);

        $request->update([
            'status' => 'rejected',
            'rejection_reason' => trim($this->rejectionReason) ?: null,
            'reviewed_by_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        try {
            Mail::to($request->email)->queue(new AccessRequestRejectedMail($request->name, $request->rejection_reason));
        } catch (\Throwable $e) {
            report($e);
        }

        $this->showRejectModal = false;
        $this->selectedRequestId = null;
        $this->feedbackMessage = "Access request from {$request->name} was rejected.";
    }

    public function createUserDirectly(): void
    {
        abort_unless(Auth::user()->isWorkspaceAdmin($this->workspace), 403);

        $this->validate([
            'newUserName' => 'required|string|max:255',
            'newUserEmail' => 'required|email|max:255|unique:users,email',
            'newUserRole' => 'required|in:admin,member,guest',
            'newUserJobTitle' => 'nullable|string|max:100',
        ]);

        $randomPassword = Str::random(32);
        $user = User::create([
            'name' => trim($this->newUserName),
            'email' => strtolower(trim($this->newUserEmail)),
            'password' => Hash::make($randomPassword),
            'job_title' => trim($this->newUserJobTitle) ?: 'Team Member',
            'timezone' => 'UTC',
        ]);

        $this->workspace->members()->attach($user->id, [
            'role' => $this->newUserRole,
            'job_title' => $user->job_title,
            'timezone' => $user->timezone,
        ]);

        $spatieRoleName = match ($this->newUserRole) {
            'admin' => 'Admin',
            'guest' => 'Guest',
            default => 'Member',
        };
        if (\Spatie\Permission\Models\Role::where('name', $spatieRoleName)->exists()) {
            $user->syncRoles([$spatieRoleName]);
        }

        $token = Password::broker()->createToken($user);
        $passwordResetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        try {
            Mail::to($user->email)->queue(new AccessRequestApprovedMail($user, $passwordResetUrl, $this->newUserRole));
        } catch (\Throwable $e) {
            report($e);
        }

        $this->showAddUserModal = false;
        $this->newUserName = '';
        $this->newUserEmail = '';
        $this->newUserRole = 'member';
        $this->newUserJobTitle = '';
        $this->feedbackMessage = "User {$user->name} created and welcome email dispatched.";
    }

    public function render()
    {
        $query = AccessRequest::query()
            ->where(function ($q) {
                $q->where('workspace_id', $this->workspace->id)
                  ->orWhereNull('workspace_id');
            });

        if ($this->tab !== 'all') {
            $query->where('status', $this->tab);
        }

        if (!empty(trim($this->search))) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('department', 'like', "%{$this->search}%");
            });
        }

        $requests = $query->with('reviewedBy')->latest()->paginate(15);

        $pendingCount = AccessRequest::where(function ($q) {
            $q->where('workspace_id', $this->workspace->id)
              ->orWhereNull('workspace_id');
        })->where('status', 'pending')->count();

        return view('livewire.access-requests.access-request-manager', [
            'requests' => $requests,
            'pendingCount' => $pendingCount,
        ]);
    }
}
