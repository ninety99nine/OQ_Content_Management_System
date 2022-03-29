<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SubscriberTopic extends Pivot
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'started_reading' => 'boolean',
        'finished_reading' => 'boolean',
    ];
}
