<?php

namespace HoomanMirghasemi\Sms\Channels;

use Exception;
use Illuminate\Notifications\Notification;
use HoomanMirghasemi\Sms\Abstracts\Driver;
use HoomanMirghasemi\Sms\SmsManager;

class SmsChannel
{
    /**
     * Send notification.
     *
     * @param $notifiable
     * @param  Notification  $notification
     * @return void
     *
     * @throws Exception
     */
    public function send($notifiable, Notification $notification): void
    {
        $manager = $notification->toSms($notifiable);
        if (! is_null($manager)) {
            $this->validate($manager);
            $manager->send();
        }
    }

    /**
     * Validate sms.
     *
     * @return void
     *
     * @throws Exception
     */
    protected function validate($manager): void
    {
        if (! $manager instanceof SmsManager && ! $manager instanceof Driver) {
            throw new Exception('Invalid data for sms notification.');
        }
    }
}
