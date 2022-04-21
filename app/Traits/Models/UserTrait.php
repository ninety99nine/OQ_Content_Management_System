<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Model;

trait UserTrait
{
    /**
     *  Check if the current authenticated user has the given permissions on the project
     */
    public function hasProjectPermissionTo($project, $permission)
    {
        $project_id = $project instanceof Model ? $project->id : $project;

        //  Get the matching project
        if( ($project = $this->projectsAsTeamMember()->where('project_id', $project_id)->first()) && $permission ) {

            //  Check if the user has the given permissions on the project
            return collect($project->pivot->permissions)->contains(function($projectPermission) use ($permission) {

                //  Check if we have all permissions or atleast the permission required
                return ($projectPermission == '*') || (strtolower($projectPermission) == strtolower($permission));

            });

        }

        return false;
    }
}
