<?php

namespace App\Livewire\Tickets;

use App\Mail\TicketAssignedMail;
use App\Mail\TicketResolvedMail;
use App\Mail\TicketStatusChangedMail;
use App\Models\AppNotification;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\Workspace;
use App\Services\TicketCapacityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketDetail extends Component
{
    use WithFileUploads;

    public ?int $ticketId = null;
    public ?Ticket $ticket = null;
    public bool $isOpen = false;

    // Editable status & priority
    public string $status = 'open';
    public string $priority = 'normal';
    public ?int $projectId = null;
    public ?int $assignedTeamId = null;
    public ?int $assignedToUserId = null;

    // Comment inputs
    public string $newCommentBody = '';
    public bool $isInternalNote = false;

    // Attachments
    public $newAttachmentFile;

    // Resolution modal
    public bool $showResolveModal = false;
    public string $resolutionSummary = '';

    // Team members capacity for assignment drawer
    public array $teamMembersCapacity = [];

    #[On('open-ticket-detail')]
    public function openTicket(int $ticketId): void
    {
        $this->ticketId = $ticketId;
        $this->loadTicket();

        if ($this->ticket) {
            $user = Auth::user();
            abort_unless($user->canViewTicket($this->ticket, $this->ticket->workspace), 403, 'Unauthorized to view this ticket.');

            $this->isOpen = true;
            $this->status = $this->ticket->status;
            $this->priority = $this->ticket->priority;
            $this->projectId = $this->ticket->project_id;
            $this->assignedTeamId = $this->ticket->assigned_team_id;
            $this->assignedToUserId = $this->ticket->assigned_to_user_id;

            if ($this->assignedTeamId) {
                $this->updateTeamCapacityMetrics();
            }
        }
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->ticket = null;
        $this->ticketId = null;
        $this->newCommentBody = '';
        $this->isInternalNote = false;
        $this->showResolveModal = false;
    }

    public function loadTicket(): void
    {
        if (!$this->ticketId) return;

        $this->ticket = Ticket::with([
            'workspace',
            'category',
            'project.space',
            'raisedBy',
            'assignedTeam.members',
            'assignedTo',
            'comments.user',
            'attachments.uploadedBy',
            'activityLogs.user',
        ])->find($this->ticketId);
    }

    public function updateTeamCapacityMetrics(): void
    {
        if (!$this->assignedTeamId) {
            $this->teamMembersCapacity = [];
            return;
        }

        $team = Team::find($this->assignedTeamId);
        if ($team) {
            $service = app(TicketCapacityService::class);
            $this->teamMembersCapacity = $service->getTeamMembersCapacity($team)->toArray();
        }
    }

    public function onTeamSelected(?int $teamId): void
    {
        $this->assignedTeamId = $teamId;
        $this->assignedToUserId = null;

        if ($teamId) {
            $team = Team::find($teamId);
            if ($team) {
                $service = app(TicketCapacityService::class);
                $this->updateTeamCapacityMetrics();

                // Suggest best candidate
                $bestAssignee = $service->suggestBestAssignee($team);
                if ($bestAssignee) {
                    $this->assignedToUserId = $bestAssignee->id;
                }
            }
        }

        $this->saveAssignment();
    }

    public function onAssigneeSelected(?int $userId): void
    {
        $this->assignedToUserId = $userId;
        $this->saveAssignment();
    }

    public function saveAssignment(): void
    {
        if (!$this->ticket) return;

        $user = Auth::user();
        $oldTeam = $this->ticket->assignedTeam?->name;
        $oldAssignee = $this->ticket->assignedTo?->name;

        $newTeam = $this->assignedTeamId ? Team::find($this->assignedTeamId) : null;
        $newAssignee = $this->assignedToUserId ? User::find($this->assignedToUserId) : null;

        $changes = [];
        if ($this->ticket->assigned_team_id !== $this->assignedTeamId) {
            $changes[] = "Team: " . ($oldTeam ?? 'None') . " -> " . ($newTeam?->name ?? 'None');
            TicketActivityLog::log($this->ticket, $user, 'assigned_team', $oldTeam, $newTeam?->name);
        }

        if ($this->ticket->assigned_to_user_id !== $this->assignedToUserId) {
            $changes[] = "Assignee: " . ($oldAssignee ?? 'Unassigned') . " -> " . ($newAssignee?->name ?? 'Unassigned');
            TicketActivityLog::log($this->ticket, $user, 'assigned_user', $oldAssignee, $newAssignee?->name);

            // Notify Assignee
            if ($newAssignee && $newAssignee->id !== $user->id) {
                AppNotification::create([
                    'user_id' => $newAssignee->id,
                    'workspace_id' => $this->ticket->workspace_id,
                    'type' => 'ticket_assigned',
                    'title' => "Ticket Assigned: {$this->ticket->ticket_number}",
                    'message' => "You have been assigned to ticket \"{$this->ticket->subject}\"",
                    'data' => ['ticket_id' => $this->ticket->id],
                ]);

                try {
                    Mail::to($newAssignee->email)->queue(new TicketAssignedMail($this->ticket));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        $updateData = [
            'assigned_team_id' => $this->assignedTeamId,
            'assigned_to_user_id' => $this->assignedToUserId,
        ];

        // If status was open and now assigned, move status to assigned
        if ($this->ticket->status === 'open' && ($this->assignedTeamId || $this->assignedToUserId)) {
            $updateData['status'] = 'assigned';
            $this->status = 'assigned';
            TicketActivityLog::log($this->ticket, $user, 'status_changed', 'open', 'assigned');
        }

        $this->ticket->update($updateData);
        $this->loadTicket();
        $this->dispatch('ticket-updated');
    }

    public function updateStatus(string $newStatus): void
    {
        if (!$this->ticket || !in_array($newStatus, ['open', 'assigned', 'in_progress', 'on_hold', 'resolved', 'closed', 'reopened'])) return;

        // If resolving, require resolution summary modal
        if ($newStatus === 'resolved') {
            $this->showResolveModal = true;
            return;
        }

        $this->applyStatusUpdate($newStatus);
    }

    public function confirmResolution(): void
    {
        $this->validate([
            'resolutionSummary' => 'required|string|min:5|max:2000',
        ]);

        $this->applyStatusUpdate('resolved', trim($this->resolutionSummary));
        $this->showResolveModal = false;
        $this->resolutionSummary = '';
    }

    public function reopenTicket(): void
    {
        if (!$this->ticket || !$this->ticket->canBeReopened()) return;

        $user = Auth::user();
        $oldStatus = $this->ticket->status;

        $this->ticket->update([
            'status' => 'reopened',
            'resolved_at' => null,
        ]);

        TicketActivityLog::log($this->ticket, $user, 'reopened', $oldStatus, 'reopened');

        // Notify Assignee
        if ($this->ticket->assignedTo) {
            AppNotification::create([
                'user_id' => $this->ticket->assigned_to_user_id,
                'workspace_id' => $this->ticket->workspace_id,
                'type' => 'ticket_reopened',
                'title' => "Ticket Reopened: {$this->ticket->ticket_number}",
                'message' => "{$user->name} reopened ticket \"{$this->ticket->subject}\"",
                'data' => ['ticket_id' => $this->ticket->id],
            ]);
        }

        $this->loadTicket();
        $this->status = 'reopened';
        $this->dispatch('ticket-updated');
    }

    public function closeTicket(): void
    {
        if (!$this->ticket) return;

        $this->applyStatusUpdate('closed');
    }

    protected function applyStatusUpdate(string $newStatus, ?string $summary = null): void
    {
        $user = Auth::user();
        $oldStatus = $this->ticket->status;

        $data = ['status' => $newStatus];

        if ($newStatus === 'resolved') {
            $data['resolved_at'] = now();
            $data['resolution_summary'] = $summary;
        } elseif ($newStatus === 'closed') {
            $data['closed_at'] = now();
        }

        $this->ticket->update($data);
        TicketActivityLog::log($this->ticket, $user, 'status_changed', $oldStatus, $newStatus);

        // Notify Requester
        if ($this->ticket->raisedBy && $this->ticket->raised_by_user_id !== $user->id) {
            $notifType = $newStatus === 'resolved' ? 'ticket_resolved' : 'ticket_status_changed';
            AppNotification::create([
                'user_id' => $this->ticket->raised_by_user_id,
                'workspace_id' => $this->ticket->workspace_id,
                'type' => $notifType,
                'title' => "Ticket {$this->ticket->ticket_number} Updated",
                'message' => "Status changed from {$oldStatus} to {$newStatus}",
                'data' => ['ticket_id' => $this->ticket->id],
            ]);

            try {
                if ($newStatus === 'resolved') {
                    Mail::to($this->ticket->raisedBy->email)->queue(new TicketResolvedMail($this->ticket));
                } else {
                    Mail::to($this->ticket->raisedBy->email)->queue(new TicketStatusChangedMail($this->ticket, $oldStatus, $newStatus));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->loadTicket();
        $this->status = $newStatus;
        $this->dispatch('ticket-updated');
    }

    public function updatePriority(string $newPriority): void
    {
        if (!$this->ticket || !in_array($newPriority, ['urgent', 'high', 'normal', 'low'])) return;

        $user = Auth::user();
        $oldPriority = $this->ticket->priority;

        $dueBy = Ticket::computeDueBy($newPriority, $this->ticket->created_at);

        $this->ticket->update([
            'priority' => $newPriority,
            'due_by' => $dueBy,
        ]);

        TicketActivityLog::log($this->ticket, $user, 'priority_changed', $oldPriority, $newPriority);

        $this->loadTicket();
        $this->priority = $newPriority;
        $this->dispatch('ticket-updated');
    }

    public function addComment(): void
    {
        if (!$this->ticket || empty(trim($this->newCommentBody))) return;

        $user = Auth::user();

        // Requester/Guest cannot post internal notes
        $isStaff = $user->isWorkspaceAdmin($this->ticket->workspace) || 
                   ($this->ticket->assigned_team_id && $user->teams()->where('teams.id', $this->ticket->assigned_team_id)->exists()) ||
                   $this->ticket->assigned_to_user_id === $user->id;

        $internalNote = $isStaff && $this->isInternalNote;

        $comment = TicketComment::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $user->id,
            'body' => trim($this->newCommentBody),
            'is_internal_note' => $internalNote,
        ]);

        TicketActivityLog::log($this->ticket, $user, 'commented', null, $internalNote ? 'Added internal note' : 'Added public reply');

        // Notify other party if public comment
        if (!$internalNote) {
            $recipientId = ($user->id === $this->ticket->raised_by_user_id)
                ? $this->ticket->assigned_to_user_id
                : $this->ticket->raised_by_user_id;

            if ($recipientId) {
                AppNotification::create([
                    'user_id' => $recipientId,
                    'workspace_id' => $this->ticket->workspace_id,
                    'type' => 'ticket_comment',
                    'title' => "New Reply on {$this->ticket->ticket_number}",
                    'message' => "{$user->name}: " . Str::limit(trim($this->newCommentBody), 100),
                    'data' => ['ticket_id' => $this->ticket->id],
                ]);
            }
        }

        $this->newCommentBody = '';
        $this->isInternalNote = false;
        $this->loadTicket();
    }

    public function uploadAttachment(): void
    {
        $this->validate([
            'newAttachmentFile' => 'required|file|max:10240',
        ]);

        if (!$this->ticket || !$this->newAttachmentFile) return;

        $user = Auth::user();
        $path = $this->newAttachmentFile->store('ticket-attachments', 'public');

        TicketAttachment::create([
            'ticket_id' => $this->ticket->id,
            'file_name' => $this->newAttachmentFile->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $this->newAttachmentFile->getSize(),
            'mime_type' => $this->newAttachmentFile->getMimeType(),
            'uploaded_by_user_id' => $user->id,
        ]);

        TicketActivityLog::log($this->ticket, $user, 'attachment_added', null, $this->newAttachmentFile->getClientOriginalName());

        $this->newAttachmentFile = null;
        $this->loadTicket();
    }

    public function updateProject(?int $newProjectId): void
    {
        if (!$this->ticket) return;

        $user = Auth::user();
        $oldProject = $this->ticket->project?->name;
        $newProject = $newProjectId ? \App\Models\Project::find($newProjectId) : null;

        $this->ticket->update(['project_id' => $newProjectId]);
        $this->projectId = $newProjectId;

        TicketActivityLog::log($this->ticket, $user, 'project_changed', $oldProject, $newProject?->name);

        $this->loadTicket();
        $this->dispatch('ticket-updated');
    }

    public function render()
    {
        $user = Auth::user();
        $workspace = $this->ticket?->workspace;

        $isStaff = $workspace ? (
            $user->isWorkspaceAdmin($workspace) ||
            ($this->ticket->assigned_team_id && $user->teams()->where('teams.id', $this->ticket->assigned_team_id)->exists()) ||
            $this->ticket->assigned_to_user_id === $user->id
        ) : false;

        $canManageAssignment = $workspace ? $user->isWorkspaceAdmin($workspace) : false;

        $teams = $workspace ? $workspace->teams()->with('members')->get() : collect();
        $projects = $workspace ? $workspace->projects()->with('space')->orderBy('name')->get() : collect();

        return view('livewire.tickets.ticket-detail', [
            'isStaff' => $isStaff,
            'canManageAssignment' => $canManageAssignment,
            'teams' => $teams,
            'projects' => $projects,
        ]);
    }
}
