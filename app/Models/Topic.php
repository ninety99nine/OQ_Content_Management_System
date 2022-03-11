<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'content', 'parent_topic_id', 'language_id', 'project_id'];

    /**
     * Get the sub-topics associated with the topic.
     */
    public function subTopics()
    {
        return $this->hasMany(Topic::class, 'parent_topic_id');
    }
    /**
     * Get the project associated with the topic.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the language associated with the topic.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the subscribers (viewers) that have read this topic
     */
    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'subscriber_topics');
    }

    //  ON DELETE EVENT
    public static function boot()
    {
        try {

            parent::boot();

            //  before delete() method call this
            static::deleting(function ($project) {

                //  Delete all topics
                $project->subTopics()->delete();

                // do the rest of the cleanup...
            });
        } catch (\Exception $e) {
            throw($e);
        }
    }
}

