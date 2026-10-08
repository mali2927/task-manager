<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class MyTickets extends Component
{
    public Workspace $workspace;
    public string $viewMode = 'assigned'; // 'assigned' (assigned to me) or 'raised' (raised by me)

    public function mount(?Workspace $workspace = null): void
    {
        if (!$workspace || !$workspace->id) {
            $this->workspace = Auth::user()->workspaces()->first() ?? Workspace::first();
        } else {
            $this->workspace = $workspace;
        }

        if (Auth::user()?->isWorkspaceRequester($this->workspace)) {
            $this->viewMode = 'raised';
            return;
        }

        // If the user has 0 assigned tickets but has raised tickets, default to 'raised'
        $assignedCount = Ticket::where('workspace_id', $this->workspace->id)
            ->where('assigned_to_user_id', Auth::id())
            ->count();

        if ($assignedCount === 0) {
            $this->viewMode = 'raised';
        }
    }

    #[On('ticket-updated')]
    public function refreshTickets(): void
    {
        // Re-renders automatically
    }

    public function render()
    {
        $userId = Auth::id();
        $isRequester = Auth::user()?->isWorkspaceRequester($this->workspace) ?? false;

        if ($isRequester) {
            $this->viewMode = 'raised';
        }

        $query = Ticket::where('workspace_id', $this->workspace->id)
            ->with(['category', 'assignedTeam', 'assignedTo', 'raisedBy']);

        if ($this->viewMode === 'assigned') {
            $query->where('assigned_to_user_id', $userId);
        } else {
            $query->where('raised_by_user_id', $userId);
        }

        $allTickets = $query->latest()->get();

        $inProgress = $allTickets->filter(fn ($t) => in_array($t->status, ['in_progress', 'assigned']));
        $open = $allTickets->filter(fn ($t) => $t->status === 'open');
        $onHold = $allTickets->filter(fn ($t) => $t->status === 'on_hold');
        $resolved = $allTickets->filter(fn ($t) => in_array($t->status, ['resolved', 'closed']));

        $overdueCount = $allTickets->filter(fn ($t) => $t->isOverdue())->count();

        $assignedTotalCount = Ticket::where('workspace_id', $this->workspace->id)
            ->where('assigned_to_user_id', $userId)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        $raisedTotalCount = Ticket::where('workspace_id', $this->workspace->id)
            ->where('raised_by_user_id', $userId)
            ->count();

        return view('livewire.tickets.my-tickets', [
            'inProgress' => $inProgress,
            'open' => $open,
            'onHold' => $onHold,
            'resolved' => $resolved,
            'overdueCount' => $overdueCount,
            'assignedTotalCount' => $assignedTotalCount,
            'raisedTotalCount' => $raisedTotalCount,
            'isRequester' => $isRequester,
        ]);
    }
}
