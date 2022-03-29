<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Topic;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    public function index(Project $project)
    {
        $topicsPayload = $project->topics()->whereIsRoot()->latest()->paginate();

        return Inertia::render('Topics/List/Main', [
            'parentTopic' => null,
            'breadcrumbs' => [],
            'topicsPayload' => $topicsPayload
        ]);

    }

    public function show (Project $project, Topic $topic)
    {
        $topicsPayload = $topic->children()->latest()->paginate();

        $breadcrumbs = collect($topic->ancestorsAndSelf($topic->id))->map(fn($topic) => collect($topic)->only(['id', 'title']))->toArray();

        return Inertia::render('Topics/List/Main', [
            'parentTopic' => $topic,
            'breadcrumbs' => $breadcrumbs,
            'topicsPayload' => $topicsPayload
        ]);

    }

    public function create(Request $request, Project $project)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'title' => ['required', 'string', 'min:5', 'max:100'],
            'content' => ['nullable', 'string', 'min:5', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'min:1', 'exists:topics,id']
        ])->validate();

        //  Set title
        $title = $request->input('title');

        //  Set content
        $content = $request->input('content');

        //  Set parent topic id
        $parent_id = $request->input('parent_id');

        //  Create new topic
        $topic = Topic::create([
            'title' => $title,
            'content' => $content,
            'project_id' => $project->id
        ]);

        if( $parentTopic = Topic::find($parent_id) ) {

            $parentTopic->prependNode($topic);

        }

        return redirect()->back()->with('topic', 'Created Successfully');
    }

    public function update(Request $request, Project $project, Topic $topic)
    {
        Validator::make($request->all(), [
            'title' => ['required', 'string', 'min:5', 'max:100'],
            'content' => ['nullable', 'string', 'min:5', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'min:1', 'exists:topics,id']
        ])->validate();

        //  Set title
        $title = $request->input('title');

        //  Set content
        $content = $request->input('content');

        //  Set parent topic id
        $parent_id = $request->input('parent_id');

        //  Update topic
        $topic->update([
            'title' => $title,
            'content' => $content,
            'project_id' => $project->id
        ]);

        if( $parentTopic = Topic::find($parent_id) ) {

            $parentTopic->prependNode($topic);

        }

        return redirect()->back()->with('topic', 'Updated Successfully');
    }

    public function delete(Project $project, Topic $topic)
    {
        //  Delete topic
        $topic->delete();

        return redirect()->back()->with('topic', 'Deleted Successfully');
    }
}
