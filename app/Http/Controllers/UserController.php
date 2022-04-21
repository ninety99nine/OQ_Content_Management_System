<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Project $project)
    {
        //  Get project permissions
        $availablePermissions = Project::PERMISSIONS;

        //  Get the project users
        $usersPayload = $project->users()->latest()->paginate(10);

        //  Render the users view
        return Inertia::render('Users/List/Main', [
            'availablePermissions' => $availablePermissions,
            'usersPayload' => $usersPayload,
        ]);
    }

    public function create(Request $request, Project $project)
    {
        //  Validate the request inputs
        $data = Validator::make($request->all(), [
            'password' => ['exclude'],
            'email' => ['required', 'email'],
            'permissions' => ['required', 'array'],
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'permissions.*' => ['string', Rule::in(Project::PERMISSIONS)],
        ])->validate();

        $user = User::where('email', $data['email'])->first();

        if( !$user ) {

            //  Set the default password
            $data['password'] = bcrypt('password');

            //  Create new user
            $user = User::create($data);

        }

        $projectAssociation = DB::table('user_projects')->where('user_id', $user->id)->where('project_id', $project->id);

        //  If the user is not associated with this project
        if( $projectAssociation->exists() == true ) {

            //  Update the user association with this project
            $projectAssociation->update([
                'permissions' => json_encode($data['permissions']),
                'updated_at' => now(),
            ]);

        }else{

            //  Associate the user with this project
            DB::table('user_projects')->insert([
                'permissions' => json_encode($data['permissions']),
                'project_id' => $project->id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function update(Request $request, Project $project, User $user)
    {
        //  Validate the request inputs
        $data = Validator::make($request->all(), [
            'password' => ['exclude'],
            'email' => ['exists:users'],
            'permissions' => ['required', 'array'],
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'permissions.*' => ['string', Rule::in(Project::PERMISSIONS)],
        ])->validate();

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function delete(Project $project, User $user)
    {
        //  Delete user association
        DB::table('user_projects')->where('user_id', $user->id)->where('project_id', $project->id)->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
