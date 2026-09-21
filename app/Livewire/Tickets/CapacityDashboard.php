<?php

namespace App\Livewire\Tickets;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Workspace;
use App\Services\TicketCapacityService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class CapacityDashboard extends Component
{
    public Workspace $workspace;

    // Edit Member Capacity Limit Modal
    public bool $showEditLimitModal = false;
    public ?int $editingTeamId = null;
    public ?int $editingUserId = null;
    public string $editingUserName = '';
    public int $newCapacityLimit = 5;

    public ?string $feedbackMessage = null;

    public function mount(Workspace $workspace): void
    {
        $this->workspace = $workspace;
        abort_unless(Auth::user()->isWorkspaceAdmin($this->workspace), 403, 'Unauthorized. Access to capacity dashboard is restricted to administrators.');
    }

    #[On('ticket-updated')]
    public function refreshMetrics(): void
    {
        // Re-renders automatically
    }

    public function openEditLimitModal(int $teamId, int $userId, string $name, int $currentLimit): void
    {
        $this->editingTeamId = $teamId;
        $this->editingUserId = $userId;
        $this->editingUserName = $name;
        $this->newCapacityLimit = $currentLimit;
        $this->showEditLimitModal = true;
    }

    public function saveCapacityLimit(): void
    {
        abort_unless(Auth::user()->isWorkspaceAdmin($this->workspace), 403);

        $this->validate([
            'newCapacityLimit' => 'required|integer|min:1|max:50',
        ]);

        $team = Team::where('workspace_id', $this->workspace->id)->find($this->editingTeamId);
        if ($team) {
            $team->members()->updateExistingPivot($this->editingUserId, [
                'capacity_limit' => $this->newCapacityLimit,
            ]);
            $this->feedbackMessage = "Capacity limit updated to {$this->newCapacityLimit} for {$this->editingUserName}.";
        }

        $this->showEditLimitModal = false;
        $this->editingTeamId = null;
        $this->editingUserId = null;
    }

    public function render()
    {
        $service = app(TicketCapacityService::class);
        $teamCapacities = $service->getWorkspaceTeamCapacities($this->workspace);

        $totalActiveTickets = $teamCapacities->sum('total_active_tickets');
        $totalWorkspaceCapacity = $teamCapacities->sum('total_capacity');
        $workspaceLoadPercentage = $totalWorkspaceCapacity > 0
            ? (int) round(($totalActiveTickets / $totalWorkspaceCapacity) * 100)
            : 0;

        return view('livewire.tickets.capacity-dashboard', [
            'teamCapacities' => $teamCapacities,
            'totalActiveTickets' => $totalActiveTickets,
            'totalWorkspaceCapacity' => $totalWorkspaceCapacity,
            'workspaceLoadPercentage' => $workspaceLoadPercentage,
        ]);
    }
}
