<?php

namespace HoomanMirghasemi\Sms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class VoiceCall.
 *
 * @method static to(string $mobileNumber);
 */
class VoiceCall extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'voiceCall';
    }
}
