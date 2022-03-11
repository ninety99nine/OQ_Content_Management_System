<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Topic;
use App\Models\Project;
use App\Models\Language;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    public function index(Request $request, Project $project, Topic $topic){

        if( $project ){

            //  Get the languages
            $languages = $project->languages()->get();

            //  Get the specified request language, otherwise the first language we can get
            $selectedLanguage = collect($languages)->filter(function($language) {

                if( request()->filled('language') ){

                    $requestInput = strtolower(request()->input('language'));

                    //  Return language specified by name or id
                    return $requestInput === strtolower($language['name']) || $requestInput == strtolower($language['id']);

                }

                //  Return all languages
                return true;

            //  Select the first language found, otherwise the first language available
            })->first() ?? $languages[0];

            //  Count the subscribers
            $totalSubscribers = $project->subscribers()->count();

            //  If we have the topic, the target the topic title
            $hasTopic = $topic->id ? true : false;

            //  Reset the topic to nothing
            $topic = $hasTopic ? $topic : null;

            //  If we have the topic, the target the sub-topics otherwise the
            $query = $hasTopic ? $topic->subTopics() : $project->mainTopics();

            //  Get the topics
            $topicsPayload = $query->where('language_id', $selectedLanguage->id)->with('language:id,name')->withCount('subscribers')->withCount('subTopics')->latest()->paginate(10);

            //  Render the topics view
            return Inertia::render('Topics/List/Main', [
                'selectedLanguage' => $selectedLanguage,
                'totalSubscribers' => $totalSubscribers,
                'topicsPayload' => $topicsPayload,
                'languages' => $languages,
                'hasTopic' => $hasTopic,
                'topic' => $topic
            ]);

        }

    }

    public function create(Request $request, Project $project){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'title' => ['required', 'string', 'min:5', 'max:100'],
                'content' => ['required', 'string', 'min:5', 'max:5000'],
                'parent_topic_id' => ['nullable', 'integer', 'min:1'],
                'language' => ['required']
            ])->validate();

            //  Set title
            $title = $request->input('title');

            //  Set content
            $content = $request->input('content');

            //  Set language id
            $language_id = $request->input('language');

            //  Set parent topic id
            $parent_topic_id = $request->input('parent_topic_id');

            //  Create new topic
            Topic::create([
                'title' => $title,
                'content' => $content,
                'project_id' => $project->id,
                'language_id' => $language_id,
                'parent_topic_id' => $parent_topic_id,
            ]);

            return redirect()->back()->with('topic', 'Created Successfully');

        }

    }

    public function update(Request $request, Project $project, $topic_id){

        if( $project ){

            Validator::make($request->all(), [
                'title' => ['required', 'string', 'min:5', 'max:100'],
                'content' => ['required', 'string', 'min:5', 'max:5000'],
                'parent_topic_id' => ['nullable', 'integer', 'min:1'],
                'language' => ['required']
            ])->validate();

            //  Set title
            $title = $request->input('title');

            //  Set content
            $content = $request->input('content');

            //  Set language id
            $language_id = $request->input('language');

            //  Set parent topic id
            $parent_topic_id = $request->input('parent_topic_id');

            //  Update topic
            Topic::findOrFail($topic_id)->update([
                'title' => $title,
                'content' => $content,
                'project_id' => $project->id,
                'language_id' => $language_id,
                'parent_topic_id' => $parent_topic_id,
            ]);

            return redirect()->back()->with('topic', 'Updated Successfully');

        }

    }

    public function delete(Project $project, $topic_id){

        if( $project ){

            //  Delete topic
            Topic::findOrFail($topic_id)->delete();

            return redirect()->back()->with('topic', 'Deleted Successfully');

        }

    }
}
