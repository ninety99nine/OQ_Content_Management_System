<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Message;
use App\Models\Project;
use App\Models\Language;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function index(Project $project){

        if( $project ){

            //  Get the languages
            $languages = $project->languages()->get();

            //  Count the subscribers
            $totalSubscribers = $project->subscribers()->count();

            //  Get the messages
            $messagesPayload = $project->messages()->with('language:id,name')->withCount('subscribers')->latest()->paginate(10);

            //  Render the messages view
            return Inertia::render('Messages/List/Main', [
                'totalSubscribers' => $totalSubscribers,
                'messagesPayload' => $messagesPayload,
                'languages' => $languages
            ]);

        }

    }

    public function create(Request $request, Project $project){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'content' => ['required', 'string', 'min:5', 'max:500'],
                'language' => ['required']
            ])->validate();

            //  Set content
            $content = $request->input('content');

            //  Set language id
            $language_id = $request->input('language');

            //  Create new message
            Message::create([
                'content' => $content,
                'project_id' => $project->id,
                'language_id' => $language_id,
            ]);

            return redirect()->back()->with('message', 'Created Successfully');

        }

    }

    public function update(Request $request, Project $project, $message_id){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'content' => ['required', 'string', 'min:5', 'max:500'],
                'language' => ['required']
            ])->validate();

            //  Set content
            $content = $request->input('content');

            //  Set language id
            $language_id = $request->input('language');

            //  Update message
            Message::findOrFail($message_id)->update([
                'content' => $content,
                'project_id' => $project->id,
                'language_id' => $language_id,
            ]);

            return redirect()->back()->with('message', 'Updated Successfully');

        }

    }

    public function delete(Project $project, $message_id){

        if( $project ){

            //  Delete message
            Message::findOrFail($message_id)->delete();

            return redirect()->back()->with('message', 'Deleted Successfully');

        }

    }
}
