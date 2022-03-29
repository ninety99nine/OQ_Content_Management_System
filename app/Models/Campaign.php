<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\CampaignTrait;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Campaign extends Model
{
    use HasFactory, CampaignTrait;

    const SCHEDULE_TYPE = [
        'Send Now',
        'Send Later',
        'Send Recurring',
    ];

    const CONTENT_TO_SEND = [
        'Specific Message',
        'Any Message'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
        'message_id_cascade' => 'array',
        'days_of_the_week' => 'array',
        'has_start_date' => 'boolean',
        'has_end_date' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'schedule_type', 'recurring_duration', 'recurring_frequency',
        'content_to_send', 'message_id_cascade', 'has_start_date', 'start_date',
        'start_time','has_end_date', 'end_date', 'end_time',
        'days_of_the_week', 'project_id'
    ];

    /**
     * Get the project associated with the campaign.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscribers associated with the campaign
     */
    public function subscribers($ids = [])
    {
        $query = $this->belongsToMany(Subscriber::class, 'campaign_subscriber');
        $query = count($ids) ? $query->whereInPivot('subscriber_ids', $ids) : $query;
        return $query;
    }

    /**
     * Get the subscribers associated with the campaign who are not ready to receive the next message.
     * These are subscribers who have received content before, but are not yet ready to receive the
     * next message because they have not satisfied the campaign schedule pattern.
     */
    public function subscribersNotReadyForNextSms()
    {
        return $this->belongsToMany(Subscriber::class, 'campaign_next_message_schedule')->using(CampaignNextMessageSchedule::class)
                    ->where('campaign_next_message_schedule.next_message_date', '>', Carbon::now())
                    ->whereNotNull('campaign_next_message_schedule.next_message_date');
    }

    /**
     * Get the subscription plans associated with the campaign
     */
    public function subscriptionPlans()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'campaign_subscription_plans');
    }

    //  ON DELETE EVENT
    public static function boot()
    {
        try {

            parent::boot();

            //  before delete() method call this
            static::deleting(function ($campaign) {

                //  Delete all records of subscription plans being assigned to this campaign
                DB::table('campaign_subscription_plans')->where(['campaign_id' => $campaign->id])->delete();

                // do the rest of the cleanup...
            });
        } catch (\Exception $e) {
            throw($e);
        }
    }

}
