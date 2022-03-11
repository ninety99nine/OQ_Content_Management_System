<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_at' => 'datetime:Y-m-d H:i:s',
        'end_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['subscriber_id', 'subscription_plan_id', 'start_at', 'end_at', 'project_id'];

    /*
     *  Scope:
     *  Return test accounts
     */
    public function scopeActive($query)
    {
        return $query->where('start_at', '<=' , Carbon::now())->where('end_at', '>=' , Carbon::now());
    }

    /**
     * Get the project associated with the subscription.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscriber associated with the subscription.
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Get the subscription plan associated with the subscription.
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     *  ATTRIBUTES
     */
    protected $appends = [
        'status'
    ];

    /**
     *  Status
     */
    public function getStatusAttribute()
    {
        return \Carbon\Carbon::parse($this->start_at)->isPast() == true &&
               \Carbon\Carbon::parse($this->end_at)->isPast() == false
                ? 'Active' : 'Inactive';
    }
}
