<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'ticket_number',
        'subject',
        'description',
        'category_id',
        'project_id',
        'priority',
        'status',
        'raised_by_user_id',
        'assigned_team_id',
        'assigned_to_user_id',
        'due_by',
        'resolution_summary',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_by' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function raisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'raised_by_user_id');
    }

    public function assignedTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'assigned_team_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->latest();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(TicketActivityLog::class)->latest('created_at');
    }

    public function isOverdue(): bool
    {
        if (in_array($this->status, ['resolved', 'closed'])) {
            return false;
        }

        return $this->due_by !== null && $this->due_by->isPast();
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function canBeReopened(): bool
    {
        if ($this->status !== 'resolved') {
            return false;
        }

        if (!$this->resolved_at) {
            return true;
        }

        return $this->resolved_at->diffInDays(now()) <= 7;
    }

    public static function slaHoursForPriority(string $priority): int
    {
        return match (strtolower($priority)) {
            'urgent' => 4,
            'high' => 24,
            'normal' => 72,
            'low' => 120,
            default => 72,
        };
    }

    public static function computeDueBy(string $priority, ?\DateTimeInterface $from = null): \Carbon\CarbonInterface
    {
        $start = $from ? \Illuminate\Support\Facades\Date::parse($from) : now();
        $hours = self::slaHoursForPriority($priority);

        return $start->addHours($hours);
    }

    public static function generateTicketNumber(int $workspaceId): string
    {
        $lastTicket = self::withTrashed()
            ->where('workspace_id', $workspaceId)
            ->latest('id')
            ->first();

        $nextNumber = 1001;
        if ($lastTicket && preg_match('/TCK-(\d+)/', $lastTicket->ticket_number, $matches)) {
            $nextNumber = max($nextNumber, (int) $matches[1] + 1);
        } else {
            $count = self::withTrashed()->where('workspace_id', $workspaceId)->count();
            $nextNumber = 1000 + $count + 1;
        }

        return "TCK-{$nextNumber}";
    }

    public function priorityBadgeColor(): string
    {
        return match (strtolower($this->priority)) {
            'urgent' => 'bg-red-500/10 text-red-500 border-red-500/20',
            'high' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
            'normal' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
            'low' => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
            default => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
        };
    }

    public function statusBadgeColor(): string
    {
        return match (strtolower($this->status)) {
            'open' => 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20',
            'assigned' => 'bg-sky-500/10 text-sky-500 border-sky-500/20',
            'in_progress' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
            'on_hold' => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
            'resolved' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            'closed' => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
            'reopened' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
            default => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
        };
    }
}
