<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_list_id',
        'parent_id',
        'created_by_id',
        'title',
        'description',
        'status_id',
        'priority', // urgent, high, normal, low
        'start_date',
        'due_date',
        'recurrence', // daily, weekly, monthly
        'estimated_hours',
        'actual_hours',
        'sort_order',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'estimated_hours' => 'float',
            'actual_hours' => 'float',
            'is_archived' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function taskList(): BelongsTo
    {
        return $this->belongsTo(TaskList::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id')->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignees')
            ->withTimestamps();
    }

    public function watchers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_watchers')
            ->withTimestamps();
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->whereNull('parent_id')->with('replies.user')->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class)->latest();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tags')
            ->withTimestamps();
    }

    public function customFieldValues(): HasMany
    {
        return $this->hasMany(TaskCustomFieldValue::class);
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    public function blockedBy(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'task_id')->where('type', 'blocked_by');
    }

    public function blocking(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'task_id')->where('type', 'blocking');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TaskTimeEntry::class)->latest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TaskActivity::class)->latest();
    }

    public function isOverdue(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        return $this->due_date->isPast() && !$this->isDone();
    }

    public function isDueToday(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        return $this->due_date->isToday() && !$this->isDone();
    }

    public function isDone(): bool
    {
        return $this->status && $this->status->type === 'done';
    }

    public function checklistStats(): array
    {
        $total = $this->checklists()->count();
        if ($total === 0) {
            return ['total' => 0, 'completed' => 0, 'percent' => 0];
        }

        $completed = $this->checklists()->where('is_completed', true)->count();
        $percent = (int) round(($completed / $total) * 100);

        return [
            'total' => $total,
            'completed' => $completed,
            'percent' => $percent,
        ];
    }

    public function priorityBadge(): array
    {
        return match ($this->priority) {
            'urgent' => ['label' => 'Urgent', 'color' => 'red', 'bg' => 'bg-red-500/15', 'text' => 'text-red-500', 'border' => 'border-red-500/30'],
            'high' => ['label' => 'High', 'color' => 'orange', 'bg' => 'bg-amber-500/15', 'text' => 'text-amber-500', 'border' => 'border-amber-500/30'],
            'normal' => ['label' => 'Normal', 'color' => 'blue', 'bg' => 'bg-blue-500/15', 'text' => 'text-blue-500', 'border' => 'border-blue-500/30'],
            'low' => ['label' => 'Low', 'color' => 'zinc', 'bg' => 'bg-zinc-500/15', 'text' => 'text-zinc-400', 'border' => 'border-zinc-500/30'],
            default => ['label' => 'Normal', 'color' => 'blue', 'bg' => 'bg-blue-500/15', 'text' => 'text-blue-500', 'border' => 'border-blue-500/30'],
        };
    }
}
