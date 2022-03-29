<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\Project;
use App\Http\Controllers\Controller;

class TopicController extends Controller
{
    public function get (Project $project)
    {
        $searchWord = request()->input('search');

        return $project->topics()->whereIsRoot()->withCount('children')->search($searchWord)->latest()->paginate();
    }

    public function show (Project $project, Topic $topic, $type = null)
    {
        $searchWord = request()->input('search');

        if( $type == 'children') {

            return $topic->children()->withCount('children')->search($searchWord)->latest()->paginate();

        }else if( $type == 'descendants') {

            return $topic->descendants()->withCount('descendants')->search($searchWord)->latest()->paginate();

        }else if( $type == 'ancestors') {

            return $topic->ancestors()->withCount('ancestors')->search($searchWord)->latest()->paginate();

        }else if( $type == 'parent') {

            return $topic->parent;

        }else{

            return $topic;

        }
    }
}
