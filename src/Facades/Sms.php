<?php

namespace HoomanMirghasemi\Sms\Facades;

use HoomanMirghasemi\Sms\Contracts\Message;
use Illuminate\Support\Facades\Facade;

/**
 * Class Sms.
 *
 * @method static to(string $mobileNumber);
 * @method static message(Message|string $message);
 * @method static send();
 */
class Sms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'sms';
    }
}
