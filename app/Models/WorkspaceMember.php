<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkspaceMember extends Pivot
{
    protected $table = 'workspace_members';

    protected $fillable = [
        'workspace_id',
        'user_id',
        'role', // owner, admin, member, guest
        'job_title',
        'timezone',
    ];
}
