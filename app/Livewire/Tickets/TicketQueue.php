<?php

namespace App\Livewire\Tickets;

use App\Mail\TicketAssignedMail;
use App\Models\AppNotification;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\Workspace;
use App\Services\TicketCapacityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TicketQueue extends Component
{
    use WithPagination;

    public Workspace $workspace;

    public string $statusTab = 'open'; // open, assigned, in_progress, resolved, closed, all
    public ?int $filterCategoryId = null;
    public ?string $filterPriority = null;
    public string $filterSla = 'all'; // all, overdue
    public string $search = '';

    // Quick Assignment Modal State
    public bool $showAssignModal = false;
    public ?int $assignTicketId = null;
    public ?Ticket $assignTicket = null;
    public ?int $selectedTeamId = null;
    public ?int $selectedAssigneeId = null;
    public array $teamMembersCapacity = [];
    public ?string $recommendedMemberName = null;

    // Toast/Feedback
    public ?string $toastMessage = null;

    public function mount(Workspace $workspace): void
    {
        $this->workspace = $workspace;
        abort_unless(Auth::user()->canTriageTickets($this->workspace), 403, 'Unauthorized. Access to triage queue is restricted to administrators.');
    }

    #[On('ticket-updated')]
    public function refreshQueue(): void
    {
        // Livewire re-renders automatically
    }

    public function openAssignModal(int $ticketId): void
    {
        $this->assignTicketId = $ticketId;
        $this->assignTicket = Ticket::find($ticketId);
        $this->selectedTeamId = $this->assignTicket?->assigned_team_id;
        $this->selectedAssigneeId = $this->assignTicket?->assigned_to_user_id;

        if ($this->selectedTeamId) {
            $this->loadTeamCapacity();
        } else {
            $this->teamMembersCapacity = [];
            $this->recommendedMemberName = null;
        }

        $this->showAssignModal = true;
    }

    public function onTeamChanged(?int $teamId): void
    {
        $this->selectedTeamId = $teamId;
        $this->selectedAssigneeId = null;

        if ($teamId) {
            $this->loadTeamCapacity();
        } else {
            $this->teamMembersCapacity = [];
            $this->recommendedMemberName = null;
        }
    }

    protected function loadTeamCapacity(): void
    {
        if (!$this->selectedTeamId) return;

        $team = Team::find($this->selectedTeamId);
        if ($team) {
            $service = app(TicketCapacityService::class);
            $capacityList = $service->getTeamMembersCapacity($team);
            $this->teamMembersCapacity = $capacityList->toArray();

            $best = $service->suggestBestAssignee($team);
            if ($best) {
                $this->selectedAssigneeId = $best->id;
                $this->recommendedMemberName = $best->name;
            }
        }
    }

    public function confirmAssignment(): void
    {
        if (!$this->assignTicketId || !$this->selectedTeamId) {
            $this->addError('selectedTeamId', 'Please select a team.');
            return;
        }

        $ticket = Ticket::findOrFail($this->assignTicketId);
        $user = Auth::user();

        $team = Team::findOrFail($this->selectedTeamId);
        $assignee = $this->selectedAssigneeId ? User::find($this->selectedAssigneeId) : null;

        $oldTeam = $ticket->assignedTeam?->name;
        $oldAssignee = $ticket->assignedTo?->name;

        $ticket->assigned_team_id = $team->id;
        $ticket->assigned_to_user_id = $assignee?->id;

        if ($ticket->status === 'open') {
            $ticket->status = 'assigned';
            TicketActivityLog::log($ticket, $user, 'status_changed', 'open', 'assigned');
        }

        TicketActivityLog::log($ticket, $user, 'assigned_team', $oldTeam, $team->name);

        if ($assignee) {
            TicketActivityLog::log($ticket, $user, 'assigned_user', $oldAssignee, $assignee->name);

            // In-App Notification
            AppNotification::create([
                'user_id' => $assignee->id,
                'workspace_id' => $this->workspace->id,
                'type' => 'ticket_assigned',
                'title' => "Ticket Assigned: {$ticket->ticket_number}",
                'message' => "You have been assigned ticket \"{$ticket->subject}\"",
                'data' => ['ticket_id' => $ticket->id],
            ]);

            try {
                Mail::to($assignee->email)->queue(new TicketAssignedMail($ticket));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $ticket->save();

        $this->showAssignModal = false;
        $this->assignTicketId = null;
        $this->toastMessage = "Ticket {$ticket->ticket_number} assigned to {$team->name}" . ($assignee ? " ({$assignee->name})" : "") . ".";
    }

    public function autoAssignBestMatch(int $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);
        $team = $ticket->assignedTeam ?? $this->workspace->teams()->first();

        if (!$team) {
            $this->toastMessage = "No teams available to assign.";
            return;
        }

        $service = app(TicketCapacityService::class);
        $bestMember = $service->suggestBestAssignee($team);

        $ticket->assigned_team_id = $team->id;
        $ticket->assigned_to_user_id = $bestMember?->id;
        if ($ticket->status === 'open') {
            $ticket->status = 'assigned';
        }
        $ticket->save();

        TicketActivityLog::log($ticket, Auth::user(), 'assigned_team', null, $team->name);
        if ($bestMember) {
            TicketActivityLog::log($ticket, Auth::user(), 'assigned_user', null, $bestMember->name);
        }

        $this->toastMessage = "Auto-assigned {$ticket->ticket_number} to {$team->name}" . ($bestMember ? " ({$bestMember->name})" : "");
    }

    public function render()
    {
        $query = Ticket::where('workspace_id', $this->workspace->id)
            ->with(['category', 'raisedBy', 'assignedTeam', 'assignedTo']);

        if ($this->statusTab !== 'all') {
            $query->where('status', $this->statusTab);
        }

        if ($this->filterCategoryId) {
            $query->where('category_id', $this->filterCategoryId);
        }

        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

        if ($this->filterSla === 'overdue') {
            $query->whereNotIn('status', ['resolved', 'closed'])
                  ->whereNotNull('due_by')
                  ->where('due_by', '<', now());
        }

        if (!empty(trim($this->search))) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('ticket_number', 'like', "%{$term}%")
                  ->orWhere('subject', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        $tickets = $query->latest()->paginate(15);

        // Stats calculation
        $openCount = Ticket::where('workspace_id', $this->workspace->id)->where('status', 'open')->count();
        $overdueCount = Ticket::where('workspace_id', $this->workspace->id)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('due_by')
            ->where('due_by', '<', now())
            ->count();
        $assignedCount = Ticket::where('workspace_id', $this->workspace->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count();

        $categories = $this->workspace->ticketCategories()->get();
        $teams = $this->workspace->teams()->with('members')->get();

        return view('livewire.tickets.ticket-queue', [
            'tickets' => $tickets,
            'openCount' => $openCount,
            'overdueCount' => $overdueCount,
            'assignedCount' => $assignedCount,
            'categories' => $categories,
            'teams' => $teams,
        ]);
    }
}
