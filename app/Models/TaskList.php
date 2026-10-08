<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskList extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_lists';

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'color',
        'sort_order',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'is_archived' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->whereNull('parent_id')->orderBy('sort_order');
    }

    public function allTasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('sort_order');
    }
}
