<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content', 'language_id', 'project_id'];

    /**
     * Get the project associated with the message.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the language associated with the message.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the subscribers that this message was sent to
     */
    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'subscriber_messages');
    }

    //  ON DELETE EVENT
    public static function boot()
    {
        try {

            parent::boot();

            //  before delete() method call this
            static::deleting(function ($message) {

                //  Delete all records of users being assigned to this project
                DB::table('subscriber_messages')->where(['message_id' => $message->id])->delete();

                // do the rest of the cleanup...
            });
        } catch (\Exception $e) {
            throw($e);
        }
    }

}
