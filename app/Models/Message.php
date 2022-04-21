<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kalnoy\Nestedset\NodeTrait;

class Message extends Model
{
    use HasFactory, NodeTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content', 'project_id'];

    public function scopeSearch($query, $searchWord)
    {
        if( !empty( $searchWord ) ){

            return $query->where('content', 'like', '%'.$searchWord.'%');

        }

        return $query;
    }

    /**
     * Get the project associated with the message.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscribers that this message was sent to
     */
    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'subscriber_messages')
                    ->withPivot(['sent_sms_count'])
                    ->withTimestamps();
    }

    //  ON DELETE EVENT
    public static function boot()
    {
        try {

            parent::boot();

            //  before delete() method call this
            static::deleting(function ($message) {

                //  Delete all records of messages being assigned to users
                DB::table('subscriber_messages')->where(['message_id' => $message->id])->delete();

                // do the rest of the cleanup...
            });
        } catch (\Exception $e) {
            throw($e);
        }
    }

}
