<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CampaignNextMessageSchedule extends Pivot
{
    protected $casts = [
        'next_message_date' => 'datetime:Y-m-d H:i:s'
    ];

    const VISIBLE_COLUMNS = [
        'id', 'next_message_date', 'sent_sms_count'
    ];
}
