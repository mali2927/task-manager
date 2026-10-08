<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar_url
 * @property string|null $job_title
 * @property string $timezone
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'avatar_url', 'job_title', 'timezone'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function avatar(): string
    {
        if ($this->avatar_url) {
            return $this->avatar_url;
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_members')
            ->withPivot(['role', 'job_title', 'timezone'])
            ->withTimestamps();
    }

    public function ownedWorkspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->using(TeamMember::class)
            ->withPivot(['role', 'capacity_limit'])
            ->withTimestamps();
    }

    public function raisedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'raised_by_user_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to_user_id');
    }

    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_assignees')
            ->withTimestamps();
    }

    public function watchedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_watchers')
            ->withTimestamps();
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TaskTimeEntry::class);
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class)->latest();
    }

    public function roleInWorkspace(Workspace $workspace): ?string
    {
        if ($this->id === $workspace->owner_id) {
            return 'owner';
        }

        $membership = $this->workspaces()->where('workspaces.id', $workspace->id)->first();
        return $membership ? $membership->pivot->role : null;
    }

    public function isWorkspaceAdmin(Workspace $workspace): bool
    {
        $role = $this->roleInWorkspace($workspace);
        return in_array($role, ['owner', 'admin']);
    }

    public function isWorkspaceRequester(?Workspace $workspace = null): bool
    {
        if ($workspace) {
            if ($this->id === $workspace->owner_id) {
                return false;
            }
            $role = $this->roleInWorkspace($workspace);
            if (in_array($role, ['requester', 'guest'])) {
                return true;
            }
            if (in_array($role, ['owner', 'admin', 'member'])) {
                return false;
            }
        }

        if ($this->hasAnyRole(['Requester', 'Guest'])) {
            return true;
        }

        $firstWs = $this->workspaces()->first();
        if ($firstWs) {
            $role = $firstWs->pivot->role ?? null;
            return in_array($role, ['requester', 'guest']);
        }

        return false;
    }

    public function isTicketOnlyUser(?Workspace $workspace = null): bool
    {
        return $this->isWorkspaceRequester($workspace);
    }

    /**
     * Check if user can invite people to the workspace.
     * Strictly restricted to Owner and Admin roles.
     */
    public function canInviteWorkspaceMembers(Workspace $workspace): bool
    {
        // 1. Workspace owner always has permission
        if ($this->id === $workspace->owner_id) {
            return true;
        }

        // 2. Workspace role must be owner or admin
        $wsRole = $this->roleInWorkspace($workspace);
        if (!in_array($wsRole, ['owner', 'admin'])) {
            return false;
        }

        // 3. Spatie DB permissions check
        if (\Spatie\Permission\Models\Permission::where('name', 'workspace.invite-members')->exists()) {
            return $this->can('workspace.invite-members') || in_array($wsRole, ['owner', 'admin']);
        }

        return true;
    }

    /**
     * Check if user can remove people from the workspace.
     * Strictly restricted to Owner and Admin roles. No one can remove the workspace owner.
     */
    public function canRemoveWorkspaceMember(Workspace $workspace, User|int $targetUser): bool
    {
        $targetUserId = is_int($targetUser) ? $targetUser : $targetUser->id;

        // No one can remove the workspace owner
        if ($targetUserId === $workspace->owner_id) {
            return false;
        }

        // 1. Workspace owner can remove any non-owner
        if ($this->id === $workspace->owner_id) {
            return true;
        }

        // 2. User must be admin in workspace
        $wsRole = $this->roleInWorkspace($workspace);
        if ($wsRole !== 'admin') {
            return false;
        }

        // 3. Spatie DB permissions check
        if (\Spatie\Permission\Models\Permission::where('name', 'workspace.remove-members')->exists()) {
            return $this->can('workspace.remove-members') || $wsRole === 'admin';
        }

        return true;
    }

    /**
     * Check if user can update a member's role in the workspace.
     * Strictly restricted to Owner and Admin roles.
     */
    public function canUpdateWorkspaceMemberRole(Workspace $workspace, User|int $targetUser): bool
    {
        $targetUserId = is_int($targetUser) ? $targetUser : $targetUser->id;

        // Cannot change owner's role unless you are the owner
        if ($targetUserId === $workspace->owner_id && $this->id !== $workspace->owner_id) {
            return false;
        }

        if ($this->id === $workspace->owner_id) {
            return true;
        }

        $wsRole = $this->roleInWorkspace($workspace);
        return in_array($wsRole, ['owner', 'admin']);
    }

    /**
     * Check if user can manage workspace teams and settings.
     * Strictly restricted to Owner and Admin roles.
     */
    public function canManageWorkspaceMembers(Workspace $workspace): bool
    {
        return $this->isWorkspaceAdmin($workspace);
    }

    public function canTriageTickets(Workspace $workspace): bool
    {
        return $this->isWorkspaceAdmin($workspace);
    }

    public function canManageTicketCategories(Workspace $workspace): bool
    {
        return $this->isWorkspaceAdmin($workspace);
    }

    public function canViewTicket(Ticket $ticket, Workspace $workspace): bool
    {
        // 1. Workspace Admins / Owners can view all tickets
        if ($this->isWorkspaceAdmin($workspace)) {
            return true;
        }

        // 2. Requester can view their own ticket
        if ($ticket->raised_by_user_id === $this->id) {
            return true;
        }

        // 3. Directly assigned user can view
        if ($ticket->assigned_to_user_id === $this->id) {
            return true;
        }

        // 4. If assigned to a team, any member of that team can view
        if ($ticket->assigned_team_id) {
            return $this->teams()->where('teams.id', $ticket->assigned_team_id)->exists();
        }

        return false;
    }

    /**
     * Determine the user's role hierarchy level in the given workspace:
     * - 'director': Director, CEO, Founder, Owner, Admin (unrestricted workspace-wide access)
     * - 'lead': Team Lead, Project Lead, Manager (access to own and supervised team records)
     * - 'member': Software Engineer, QA, Member, Guest (strictly personal records only)
     */
    public function getAiHierarchyTier(Workspace $workspace): string
    {
        // 1. Workspace Owner is top executive / Director tier
        if ($this->id === $workspace->owner_id) {
            return 'director';
        }

        // 2. Workspace role in pivot
        $membership = $this->workspaces()->where('workspaces.id', $workspace->id)->first();
        $wsRole = strtolower($membership?->pivot->role ?? '');
        if (in_array($wsRole, ['owner', 'admin'])) {
            return 'director';
        }

        // 3. Spatie roles
        if ($this->hasAnyRole(['Owner', 'Admin'])) {
            return 'director';
        }

        // 4. Job title keywords for Executive / Director / CEO level
        $jobTitle = strtolower((string) ($membership?->pivot->job_title ?: $this->job_title));
        $directorKeywords = ['director', 'ceo', 'cto', 'coo', 'cfo', 'founder', 'architect', 'head of', 'vice president', 'vp', 'executive'];
        foreach ($directorKeywords as $keyword) {
            if (str_contains($jobTitle, $keyword)) {
                return 'director';
            }
        }

        // 5. Team Lead / Manager
        $isTeamLead = $this->teams()
            ->where('teams.workspace_id', $workspace->id)
            ->wherePivot('role', 'lead')
            ->exists();

        if ($isTeamLead || str_contains($jobTitle, 'lead') || str_contains($jobTitle, 'manager') || str_contains($jobTitle, 'supervisor')) {
            return 'lead';
        }

        // 6. Default: Member (Software Engineer, QA, Contributor, Guest)
        return 'member';
    }

    public function getAiHierarchyLabel(Workspace $workspace): string
    {
        $membership = $this->workspaces()->where('workspaces.id', $workspace->id)->first();
        $jobTitle = $membership?->pivot->job_title ?: $this->job_title ?: 'Member';
        $tier = $this->getAiHierarchyTier($workspace);

        return match ($tier) {
            'director' => "Director / Executive ({$jobTitle})",
            'lead' => "Team Lead ({$jobTitle})",
            default => "Member ({$jobTitle})",
        };
    }

    public function canAccessTaskInHierarchy(Task $task, Workspace $workspace): bool
    {
        $tier = $this->getAiHierarchyTier($workspace);

        if ($tier === 'director') {
            return true;
        }

        if ($tier === 'lead') {
            $teamIds = $this->teams()->where('teams.workspace_id', $workspace->id)->wherePivot('role', 'lead')->pluck('teams.id');
            $teamMemberIds = \App\Models\TeamMember::whereIn('team_id', $teamIds)->pluck('user_id');

            $isAssignedToTeamMember = $task->assignees()->whereIn('users.id', $teamMemberIds)->exists();
            $isAssignedToSelf = $task->assignees()->where('users.id', $this->id)->exists();
            $isCreatedBySelf = $task->created_by_id === $this->id;

            return $isAssignedToSelf || $isCreatedBySelf || $isAssignedToTeamMember;
        }

        // Member tier: can ONLY access tasks assigned to them, created by them, or watched by them
        $isAssigned = $task->assignees()->where('users.id', $this->id)->exists();
        $isCreator = $task->created_by_id === $this->id;
        $isWatcher = $task->watchers()->where('users.id', $this->id)->exists();

        return $isAssigned || $isCreator || $isWatcher;
    }
}
