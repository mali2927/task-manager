<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class TicketCapacityService
{
    /**
     * Get capacity and workload metrics for all members of a specific team.
     *
     * @return Collection<int, array{
     *     user: User,
     *     active_tickets: int,
     *     capacity_limit: int,
     *     load_percentage: int,
     *     is_at_capacity: bool,
     *     is_over_capacity: bool,
     *     badge_color: string
     * }>
     */
    public function getTeamMembersCapacity(Team $team): Collection
    {
        $members = $team->members()->get();

        return $members->map(function (User $member) use ($team) {
            $capacityLimit = (int) ($member->pivot->capacity_limit ?: 5);

            $activeTicketsCount = Ticket::where('assigned_to_user_id', $member->id)
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count();

            $percentage = $capacityLimit > 0
                ? (int) round(($activeTicketsCount / $capacityLimit) * 100)
                : 100;

            $isAtCapacity = $activeTicketsCount >= $capacityLimit;
            $isOverCapacity = $activeTicketsCount > $capacityLimit;

            $badgeColor = match (true) {
                $isOverCapacity => 'red',
                $isAtCapacity => 'amber',
                $percentage >= 60 => 'yellow',
                default => 'emerald',
            };

            return [
                'user' => $member,
                'user_id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'avatar' => $member->avatar(),
                'role' => $member->pivot->role,
                'active_tickets' => $activeTicketsCount,
                'capacity_limit' => $capacityLimit,
                'load_percentage' => $percentage,
                'is_at_capacity' => $isAtCapacity,
                'is_over_capacity' => $isOverCapacity,
                'badge_color' => $badgeColor,
            ];
        })->sortBy('load_percentage')->values();
    }

    /**
     * Suggest the best team member for ticket assignment based on lowest load ratio.
     */
    public function suggestBestAssignee(Team $team): ?User
    {
        $membersCapacity = $this->getTeamMembersCapacity($team);

        if ($membersCapacity->isEmpty()) {
            return null;
        }

        // Return user with lowest load percentage (and lowest active count on ties)
        return $membersCapacity->first()['user'];
    }

    /**
     * Get team workload overview for the entire workspace.
     */
    public function getWorkspaceTeamCapacities(Workspace $workspace): Collection
    {
        return $workspace->teams()->with('members')->get()->map(function (Team $team) {
            $membersData = $this->getTeamMembersCapacity($team);
            $totalActive = $membersData->sum('active_tickets');
            $totalCapacity = $membersData->sum('capacity_limit');
            $overallPercentage = $totalCapacity > 0
                ? (int) round(($totalActive / $totalCapacity) * 100)
                : 0;

            return [
                'team' => $team,
                'members' => $membersData,
                'total_active_tickets' => $totalActive,
                'total_capacity' => $totalCapacity,
                'overall_percentage' => $overallPercentage,
            ];
        });
    }
}
