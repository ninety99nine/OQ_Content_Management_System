<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use App\Models\Project;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function get (Project $project)
    {
        $searchWord = request()->input('search');

        return $project->messages()->whereIsRoot()->withCount('children')->search($searchWord)->latest()->paginate();
    }

    public function show (Project $project, Message $message, $type = null)
    {
        $searchWord = request()->input('search');

        if( $type == 'children') {

            return $message->children()->withCount('children')->search($searchWord)->latest()->paginate();

        }else if( $type == 'descendants') {

            return $message->descendants()->withCount('descendants')->search($searchWord)->latest()->paginate();

        }else if( $type == 'ancestors') {

            return $message->ancestors()->withCount('ancestors')->search($searchWord)->latest()->paginate();

        }else if( $type == 'parent') {

            return $message->parent;

        }else{

            return $message;

        }
    }
}
