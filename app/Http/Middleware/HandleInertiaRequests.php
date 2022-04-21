<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\Project;
use Inertia\Middleware;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $projectPermissions = [];

        //  If the project is provided on the request
        if( request()->project ) {

            /**
             *  Get the current authenticated user
             *
             *  @var User $user
             */
            $user = auth()->user();

            /**
             *  Get the current authenticated user's project matching the current project id
             *
             *  @var Project $project
             */
            $project = $user->projectsAsTeamMember()->where('project_id', request()->project->id)->first();

            /**
             *  Get the current authenticated user's project permissions
             *
             *  @var Project $project
             */
            $projectPermissions = $project->pivot->permissions;

        }

        return array_merge(parent::share($request), [

            'projectPermissions' => $projectPermissions

        ]);
    }
}
