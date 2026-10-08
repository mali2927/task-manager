<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'space_id',
        'name',
        'description',
        'color',
        'icon',
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

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function lists(): HasMany
    {
        return $this->hasMany(TaskList::class)->orderBy('sort_order');
    }

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class, TaskList::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
