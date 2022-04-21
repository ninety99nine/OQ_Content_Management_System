<?php

namespace App\Models\Pivots;

use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectUserAsTeamMember extends Pivot
{
    protected $casts = [
        'permissions' => 'array'
    ];

    const VISIBLE_COLUMNS = [
        'id', 'permissions'
    ];

    public function getPermissionsAttribute($permissions)
    {
        $permissions = json_decode($permissions ?? '[]');

        return collect($permissions)->contains('*')
            //  Get every permission available except the "*" permission
            ? collect(Project::PERMISSIONS)->filter(fn($permission) => $permission !== '*')->all()
            //  Get only the specified permissions
            : $permissions;
    }
}
