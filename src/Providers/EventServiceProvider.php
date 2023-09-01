<?php

namespace HoomanMirghasemi\Sms\Providers;

use HoomanMirghasemi\Sms\Events\ProviderConnectionFailedEvent;
use HoomanMirghasemi\Sms\Events\SmsSentEvent;
use HoomanMirghasemi\Sms\Listeners\DbLogListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SmsSentEvent::class => [
            DbLogListener::class,
        ],
        ProviderConnectionFailedEvent::class => [
            DbLogListener::class,
        ],
    ];
}
