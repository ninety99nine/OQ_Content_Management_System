<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        //  Get the user projects
        $projectsPayload = Auth::user()->projects()->paginate(10);

        //  Render the projects view
        return Inertia::render('Projects/List/Main', [
            'projectsPayload' => $projectsPayload
        ]);
    }

    public function create(Request $request)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:5', 'max:500']
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set description
        $description = $request->input('description');

        //  Project settings
        $settings = [
            'active' => true,
        ];

        //  Create new project
        $project = Project::create([
            'name' => $name,
            'settings' => $settings,
            'description' => $description,
        ]);

        //  Add user to project
        DB::table('user_projects')->insert([
            'project_id' => $project->id,
            'user_id' => Auth::user()->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function update(Request $request, Project $project)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:5', 'max:500']
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set description
        $description = $request->input('description');

        //  Set settings
        $settings = $request->input('settings');

        //  Update project
        $project->update([
            'name' => $name,
            'settings' => $settings,
            'description' => $description
        ]);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function delete(Request $request, Project $project)
    {
        //  Delete project
        $project->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
