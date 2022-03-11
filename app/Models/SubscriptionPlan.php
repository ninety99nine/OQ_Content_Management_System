<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'price', 'frequency', 'duration', 'project_id'];

    /**
     * Get the project associated with the subscription plan.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subscriptions associated with the subscription plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     *  Accessors
     */

    public function getPriceAttribute($value)
    {
        return number_format($value, 2);
    }

    public function getFrequencyAttribute($value)
    {
        /*
         *  If the duration is equal to 1, then cut the frequency text
         *  from "days", "weeks", "months", and "years" to
         *  "day", "week", "month", and "year". Then
         *  capitalize the result.
         */
        return ucfirst( $this->duration == 1 ? substr($value, 0, -1) : $value );
    }

    /**
     *  Mutators
     */

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = number_format($value, 2);
    }
}
