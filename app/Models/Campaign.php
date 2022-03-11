<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Campaign extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
        'has_start_date' => 'boolean',
        'has_end_date' => 'boolean',
        'days_of_the_week' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'duration', 'frequency',
        'has_start_date', 'start_date', 'start_time',
        'has_end_date', 'end_date', 'end_time',
        'days_of_the_week',
        'project_id'
    ];

    /**
     * Get the project associated with the campaign.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
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
