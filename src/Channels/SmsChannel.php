<?php

namespace HoomanMirghasemi\Sms\Channels;

use HoomanMirghasemi\Sms\Abstracts\Driver;
use HoomanMirghasemi\Sms\SmsManager;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    /**
     * Send notification.
     *
     * @throws \Exception
     */
    public function send($notifiable, Notification $notification): void
    {
        $manager = $notification->toSms($notifiable);
        if (!is_null($manager)) {
            $this->validate($manager);
            $manager->send();
        }
    }

    /**
     * Validate sms.
     *
     * @throws \Exception
     */
    protected function validate($manager): void
    {
        if (!$manager instanceof SmsManager && !$manager instanceof Driver) {
            throw new \Exception('Invalid data for sms notification.');
        }
    }
}
