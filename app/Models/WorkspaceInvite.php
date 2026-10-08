<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WorkspaceInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'email',
        'role',
        'token',
        'status',
        'invited_by_id',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($invite) {
            if (empty($invite->token)) {
                $invite->token = Str::random(32);
            }
            if (empty($invite->expires_at)) {
                $invite->expires_at = now()->addDays(7);
            }
        });
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }
}
