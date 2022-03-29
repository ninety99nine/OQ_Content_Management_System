<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kalnoy\Nestedset\NodeTrait;

class Topic extends Model
{
    use HasFactory, NodeTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'content', 'project_id'];

    public function scopeSearch($query, $searchWord)
    {
        if( !empty( $searchWord ) ){

            return $query->where('title', 'like', '%'.$searchWord.'%');

        }

        return $query;
    }

    /**
     * Get the project associated with the topic.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
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
            static::deleting(function ($topic) {

                //  Delete all records of topics being assigned to users
                DB::table('subscriber_topics')->where(['topic_id' => $topic->id])->delete();

                // do the rest of the cleanup...
            });
        } catch (\Exception $e) {

            throw($e);

        }
    }
}

