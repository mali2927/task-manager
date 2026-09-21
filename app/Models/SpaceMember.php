<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SpaceMember extends Pivot
{
    protected $table = 'space_members';

    protected $fillable = [
        'space_id',
        'user_id',
        'role_override', // admin, member, guest
    ];
}
