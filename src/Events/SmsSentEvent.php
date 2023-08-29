<?php

namespace HoomanMirghasemi\Sms\Events;

use HoomanMirghasemi\Sms\Abstracts\Driver;
use HoomanMirghasemi\Sms\Contracts\SmsEvent;

class SmsSentEvent implements SmsEvent
{
    public function __construct(public Driver $smsDriver)
    {
    }
}
