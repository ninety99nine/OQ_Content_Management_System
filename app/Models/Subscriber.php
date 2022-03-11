<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscriber extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['msisdn', 'project_id'];

    public function scopeHasActiveSubscription($query)
    {
        return $query->whereHas('subscriptions', function (Builder $query) {
            $query->active();
        });
    }

    public function scopeHasReceivedMessage($query)
    {
        /**
         *  Question: Do we have a dmessage that was created
         *  between now and 24 hours ago?
         *
         *  If we do then we have received our dmessage
         */
        return $query->whereHas('messages', function (Builder $query) {

            //  Target the "created_at" field of the "subscriber_messages" table
            $query->wherePivot('subscriber_messages.created_at', '>', Carbon::now()->subMinutes(1));

        });
    }

    public function scopeHasNotReceivedMessage($query)
    {
        /**
         *  Question: Do we have a dmessage that was created
         *  between now and 24 hours ago?
         *
         *  If we don't then we have not received our dmessage
         */
        return $query->whereDoesntHave('messages', function (Builder $query) {

            //  Target the "created_at" field of the "subscriber_messages" table
            $query->where('subscriber_messages.created_at', '>', Carbon::now()->subMinutes(1));

        });
    }

    /**
     * Get the project associated with the subscriber.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the dmessages that this subscriber received
     */
    public function messages()
    {
        return $this->belongsToMany(Message::class, 'subscriber_messages')->withTimestamps();
    }

    /**
     * Get the lastest dmessage that this subscriber received
     */
    public function lastestMessages()
    {
        return $this->messages()->latest();
    }

    /**
     * Get the subscriber lists of this subscriber
     */
    public function subscriberLists()
    {
        return $this->belongsToMany(SubscriberList::class, 'subscriber_list_distribution');
    }

    /**
     * Get the subscriptions associated with the subscriber.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the lastest subscription that this subscriber received
     */
    public function lastestSubscriptions()
    {
        return $this->subscriptions()->latest();
    }    //  ON DELETE EVENT
    public static function boot()
    {
        try {

            parent::boot();

            //  before delete() method call this
            static::deleting(function ($subscriber) {

                DB::table('subscriber_messages')->where(['subscriber_id' => $subscriber->id])->delete();
                DB::table('subscriber_list_distribution')->where(['subscriber_id' => $subscriber->id])->delete();

                // do the rest of the cleanup...
            });
        } catch (\Exception $e) {
            throw($e);
        }
    }

}
