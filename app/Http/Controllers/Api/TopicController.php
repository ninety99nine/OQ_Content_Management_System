<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class TopicController extends Controller
{
    public function get (Request $request, Project $project, $topic = null)
    {
        $withSubTopics = request()->input('withSubTopics') === '1';
        $language = request()->input('language');

        if( !is_null($topic)){

            $topics = $project->topics()->where('id', $topic)->first()->subTopics();

        }else{

            $topics = $project->topics()->whereNull('parent_topic_id');

        }

        if( $withSubTopics ){

            $topics = $topics->with('subTopics');

        }else{

            $topics = $topics->withCount('subTopics');

        }

        if( $language ){

            $topics = $topics->whereHas('language', function (Builder $query) use ($language) {
                $query->where('name', $language);
            });

        }

        return $topics->paginate();
    }

    public function show (Project $project, $topic)
    {
        $withSubTopics = request()->input('withSubTopics') === '1';

        $topic = $project->topics()->where('id', $topic);

        if( $withSubTopics ){

            $topic = $topic->with('subTopics');

        }

        return $topic->first();
    }
}
