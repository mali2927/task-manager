<?php

namespace App\Livewire\Workspace;

use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvite;
use Illuminate\Support\Facades\Auth;
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
            'inviteRole' => 'required|in:admin,member,guest',
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
        }

        $this->inviteSuccessMessage = "Invitation created successfully for {$this->inviteEmail}.";
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

        if (!in_array($newRole, ['owner', 'admin', 'member', 'guest'])) return;

        // Prevent modifying owner unless logged in user is owner
        if ($this->workspace->owner_id === $userId && Auth::id() !== $userId) return;

        $this->workspace->members()->updateExistingPivot($userId, [
            'role' => $newRole,
        ]);
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
