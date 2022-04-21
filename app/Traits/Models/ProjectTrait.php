<?php

namespace App\Traits\Models;

trait ProjectTrait
{
    public function hasSmsCredentials()
    {
        return !empty($this->settings['sms_username']) &&
               !empty($this->settings['sms_password']) &&
               !empty($this->settings['sms_sender']);
    }
}
