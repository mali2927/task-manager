<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamMember extends Pivot
{
    protected $table = 'team_members';

    protected $fillable = [
        'team_id',
        'user_id',
        'role', // lead, member
        'capacity_limit',
    ];

    protected function casts(): array
    {
        return [
            'capacity_limit' => 'integer',
        ];
    }
}
