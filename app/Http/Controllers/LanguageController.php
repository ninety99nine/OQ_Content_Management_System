<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function index(Project $project){

        if( $project ){

            //  Count the messages
            $totalMessages = $project->messages()->count();

            //  Get the languages
            $languages = $project->languages()->withCount('messages')->latest()->paginate(10);

            //  Render the languages view
            return Inertia::render('Languages/List/Main', [
                'totalMessages' => $totalMessages,
                'languagesPayload' => $languages
            ]);

        }

    }

    public function create(Request $request, Project $project){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:5', 'max:30', Rule::unique('languages')->where(function ($query) use ($request, $project) {

                    //  Make sure that this project does not already have this language
                    return $query->where('name', $request->input('name'))->where('project_id', $project->id);

                })],
            ])->validate();

            //  Set name
            $name = $request->input('name');

            //  Create new language
            Language::create([
                'name' => $name,
                'project_id' => $project->id
            ]);

            return redirect()->back()->with('message', 'Created Successfully');

        }

    }

    public function update(Request $request, Project $project, $language_id){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:5', 'max:30', Rule::unique('languages')->where(function ($query) use ($request, $project) {

                    //  Make sure that this project does not already have this language
                    return $query->where('name', $request->input('name'))->where('project_id', $project->id);

                })],
            ])->validate();

            //  Set name
            $name = $request->input('name');

            //  Update language
            Language::findOrFail($language_id)->update([
                'name' => $name,
                'project_id' => $project->id
            ]);

            return redirect()->back()->with('message', 'Updated Successfully');

        }
    }

    public function delete(Project $project, $language_id){

        if( $project ){

            //  Delete language
            Language::findOrFail($language_id)->delete();

            return redirect()->back()->with('message', 'Deleted Successfully');

        }

    }
}
