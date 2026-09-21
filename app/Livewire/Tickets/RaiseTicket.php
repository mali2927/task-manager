<?php

namespace App\Livewire\Tickets;

use App\Mail\TicketRaisedAdminMail;
use App\Models\AppNotification;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class RaiseTicket extends Component
{
    use WithFileUploads;

    public Workspace $workspace;

    public string $subject = '';
    public string $description = '';
    public ?int $categoryId = null;
    public string $priority = 'normal'; // urgent, high, normal, low
    public array $attachments = [];

    public bool $isSubmitted = false;
    public ?string $createdTicketNumber = null;

    public function mount(Workspace $workspace): void
    {
        $this->workspace = $workspace;

        $defaultCategory = $this->workspace->ticketCategories()->first();
        if ($defaultCategory) {
            $this->categoryId = $defaultCategory->id;
        }
    }

    public function submit(): void
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'categoryId' => 'nullable|exists:ticket_categories,id',
            'priority' => 'required|in:urgent,high,normal,low',
            'attachments.*' => 'nullable|file|max:10240', // 10MB each
        ]);

        $user = Auth::user();
        $ticketNumber = Ticket::generateTicketNumber($this->workspace->id);
        $dueBy = Ticket::computeDueBy($this->priority);

        // Pre-route to default team if category specifies one
        $category = $this->categoryId ? TicketCategory::find($this->categoryId) : null;
        $defaultTeamId = $category?->default_team_id;

        $ticket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => $ticketNumber,
            'subject' => trim($this->subject),
            'description' => trim($this->description),
            'category_id' => $this->categoryId,
            'priority' => $this->priority,
            'status' => 'open',
            'raised_by_user_id' => $user->id,
            'assigned_team_id' => $defaultTeamId,
            'due_by' => $dueBy,
        ]);

        // Save attachments
        foreach ($this->attachments as $file) {
            if ($file) {
                $path = $file->store('ticket-attachments', 'public');
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by_user_id' => $user->id,
                ]);
            }
        }

        // Activity Log
        TicketActivityLog::log($ticket, $user, 'created', null, "Ticket created with {$this->priority} priority");

        // Notify Admins (In-App + Queued Email)
        $adminMembers = $this->workspace->members()
            ->wherePivotIn('role', ['owner', 'admin'])
            ->get();

        foreach ($adminMembers as $admin) {
            AppNotification::create([
                'user_id' => $admin->id,
                'workspace_id' => $this->workspace->id,
                'type' => 'ticket_raised',
                'title' => "New Support Ticket: {$ticket->ticket_number}",
                'message' => "{$user->name} raised a ticket: \"{$ticket->subject}\"",
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'priority' => $ticket->priority,
                ],
            ]);

            try {
                Mail::to($admin->email)->queue(new TicketRaisedAdminMail($ticket));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->isSubmitted = true;
        $this->createdTicketNumber = $ticketNumber;
    }

    public function resetForm(): void
    {
        $this->subject = '';
        $this->description = '';
        $this->priority = 'normal';
        $this->attachments = [];
        $this->isSubmitted = false;
        $this->createdTicketNumber = null;
    }

    public function render()
    {
        $categories = $this->workspace->ticketCategories()->get();

        return view('livewire.tickets.raise-ticket', [
            'categories' => $categories,
        ]);
    }
}
