<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CampaignNextMessageSchedule extends Pivot
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'next_message_date' => 'datetime:Y-m-d H:i:s'
    ];
}
