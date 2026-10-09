<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'action',
        'from_value',
        'to_value',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDescriptionAttribute(): string
    {
        return match ($this->action) {
            'created' => $this->to_value ?: 'Ticket created',
            'status_changed' => "Status changed" . ($this->from_value ? " from '{$this->from_value}'" : "") . " to '{$this->to_value}'",
            'assigned_team' => "Assigned to team '{$this->to_value}'",
            'assigned_user' => "Assigned to '{$this->to_value}'",
            'priority_changed' => "Priority changed" . ($this->from_value ? " from '{$this->from_value}'" : "") . " to '{$this->to_value}'",
            'reopened' => 'Ticket reopened by requester',
            'resolved' => 'Ticket marked as resolved',
            'closed' => 'Ticket closed',
            'commented' => $this->to_value ?: 'Added a comment/reply',
            'attachment_added' => "Uploaded attachment: {$this->to_value}",
            'project_changed' => "Project changed" . ($this->from_value ? " from '{$this->from_value}'" : "") . " to '{$this->to_value}'",
            'converted_to_task' => "Converted to task: {$this->to_value}",
            'linked_to_task' => "Linked to task: {$this->to_value}",
            'unlinked_from_task' => "Unlinked from task: {$this->to_value}",
            'created_from_task' => "Created from task: {$this->to_value}",
            'csat_rated' => "Satisfaction rated: {$this->to_value}",
            default => $this->to_value ?: ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    public static function log(Ticket $ticket, ?User $user, string $action, ?string $from = null, ?string $to = null): self
    {
        return self::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user?->id,
            'action' => $action,
            'from_value' => $from,
            'to_value' => $to,
            'created_at' => now(),
        ]);
    }
}
