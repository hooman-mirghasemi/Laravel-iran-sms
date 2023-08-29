<?php

namespace HoomanMirghasemi\Sms\Contracts;

use HoomanMirghasemi\Sms\Abstracts\Driver;

interface SmsEvent
{
    public function __construct(Driver $smsDriver);
}
