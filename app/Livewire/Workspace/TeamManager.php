<?php

namespace App\Livewire\Workspace;

use App\Mail\AccessRequestApprovedMail;
use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TeamManager extends Component
{
    public Workspace $workspace;

    // New Member Invite
    public bool $showInviteModal = false;
    public string $inviteEmail = '';
    public string $inviteRole = 'member';
    public ?string $inviteSuccessMessage = null;

    // New Team Modal
    public bool $showTeamModal = false;
    public string $newTeamName = '';
    public string $newTeamDescription = '';
    public string $newTeamColor = '#6366f1';
    public array $newTeamMemberIds = [];

    public function mount(Workspace $workspace): void
    {
        $this->workspace = $workspace;
        if (Auth::user()?->isWorkspaceRequester($this->workspace)) {
            $this->redirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]), navigate: true);
            return;
        }
    }

    public function inviteMember(): void
    {
        abort_unless(
            Auth::user()->canInviteWorkspaceMembers($this->workspace),
            403,
            'Unauthorized. Only workspace owners and admins can invite people.'
        );

        $this->validate([
            'inviteEmail' => 'required|email',
            'inviteRole' => 'required|in:admin,member,guest,requester',
        ]);

        // Check if existing member
        $existing = User::where('email', $this->inviteEmail)->first();
        if ($existing && $this->workspace->members()->where('users.id', $existing->id)->exists()) {
            $this->addError('inviteEmail', 'This user is already a member of this workspace.');
            return;
        }

        $invite = WorkspaceInvite::create([
            'workspace_id' => $this->workspace->id,
            'email' => strtolower(trim($this->inviteEmail)),
            'role' => $this->inviteRole,
            'invited_by_id' => Auth::id(),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // If user already exists in platform, add them automatically
        if ($existing) {
            $this->workspace->members()->attach($existing->id, [
                'role' => $this->inviteRole,
                'job_title' => $existing->job_title,
                'timezone' => $existing->timezone,
            ]);
            $invite->update(['status' => 'accepted']);
        } else {
            // Provision user account and dispatch invitation email
            $randomPassword = Str::random(32);
            $email = strtolower(trim($this->inviteEmail));
            $namePart = explode('@', $email)[0];
            $user = User::create([
                'name' => ucwords(str_replace(['.', '_', '-'], ' ', $namePart)),
                'email' => $email,
                'password' => Hash::make($randomPassword),
                'job_title' => in_array($this->inviteRole, ['guest', 'requester']) ? 'Requester' : 'Team Member',
                'timezone' => 'UTC',
            ]);

            $this->workspace->members()->attach($user->id, [
                'role' => $this->inviteRole,
                'job_title' => $user->job_title,
                'timezone' => $user->timezone,
            ]);

            $spatieRoleName = match ($this->inviteRole) {
                'admin' => 'Admin',
                'guest', 'requester' => \Spatie\Permission\Models\Role::where('name', 'Requester')->exists() ? 'Requester' : 'Guest',
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
                Mail::to($user->email)->queue(new AccessRequestApprovedMail($user, $passwordResetUrl, $this->inviteRole));
            } catch (\Throwable $e) {
                report($e);
            }

            $invite->update(['status' => 'accepted']);
        }

        $this->inviteSuccessMessage = "Invitation sent successfully via email to {$this->inviteEmail}.";
        $this->inviteEmail = '';
        $this->showInviteModal = false;
    }

    public function updateMemberRole(int $userId, string $newRole): void
    {
        abort_unless(
            Auth::user()->canUpdateWorkspaceMemberRole($this->workspace, $userId),
            403,
            'Unauthorized. Only workspace owners and admins can update member roles.'
        );

        if (!in_array($newRole, ['owner', 'admin', 'member', 'guest', 'requester'])) return;

        // Prevent modifying owner unless logged in user is owner
        if ($this->workspace->owner_id === $userId && Auth::id() !== $userId) return;

        $this->workspace->members()->updateExistingPivot($userId, [
            'role' => $newRole,
        ]);

        $targetUser = User::find($userId);
        if ($targetUser) {
            $spatieRoleName = match ($newRole) {
                'owner' => 'Owner',
                'admin' => 'Admin',
                'guest', 'requester' => \Spatie\Permission\Models\Role::where('name', 'Requester')->exists() ? 'Requester' : 'Guest',
                default => 'Member',
            };
            if (\Spatie\Permission\Models\Role::where('name', $spatieRoleName)->exists()) {
                $targetUser->syncRoles([$spatieRoleName]);
            }
        }
    }

    public function removeMember(int $userId): void
    {
        abort_unless(
            Auth::user()->canRemoveWorkspaceMember($this->workspace, $userId),
            403,
            'Unauthorized. Only workspace owners and admins can remove people.'
        );

        if ($userId === $this->workspace->owner_id) return;

        $this->workspace->members()->detach($userId);
    }

    public function createTeam(): void
    {
        abort_unless(
            Auth::user()->canManageWorkspaceMembers($this->workspace),
            403,
            'Unauthorized. Only workspace owners and admins can create teams.'
        );

        $this->validate([
            'newTeamName' => 'required|string|max:100',
        ]);

        $team = Team::create([
            'workspace_id' => $this->workspace->id,
            'name' => trim($this->newTeamName),
            'description' => trim($this->newTeamDescription) ?: null,
            'color' => $this->newTeamColor,
        ]);

        if (!empty($this->newTeamMemberIds)) {
            $team->members()->sync($this->newTeamMemberIds);
        }

        $this->showTeamModal = false;
        $this->newTeamName = '';
        $this->newTeamDescription = '';
        $this->newTeamMemberIds = [];
    }

    public function deleteTeam(int $teamId): void
    {
        abort_unless(
            Auth::user()->canManageWorkspaceMembers($this->workspace),
            403,
            'Unauthorized. Only workspace owners and admins can delete teams.'
        );

        $team = Team::find($teamId);
        if ($team && $team->workspace_id === $this->workspace->id) {
            $team->delete();
        }
    }

    public function render()
    {
        $user = Auth::user();
        $canInvite = $user ? $user->canInviteWorkspaceMembers($this->workspace) : false;
        $canManage = $user ? $user->canManageWorkspaceMembers($this->workspace) : false;
        $members = $this->workspace->members;
        $teams = $this->workspace->teams()->with('members')->get();
        $invites = $this->workspace->invites()->where('status', 'pending')->latest()->get();

        return view('livewire.workspace.team-manager', [
            'members' => $members,
            'teams' => $teams,
            'invites' => $invites,
            'canInvite' => $canInvite,
            'canManage' => $canManage,
        ]);
    }
}
