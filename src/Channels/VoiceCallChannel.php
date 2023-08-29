<?php

namespace HoomanMirghasemi\Sms\Channels;

use Exception;
use Illuminate\Notifications\Notification;
use HoomanMirghasemi\Sms\Abstracts\Driver;
use HoomanMirghasemi\Sms\VoiceCallManager;

class VoiceCallChannel
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
        $manager = $notification->toVoiceCall($notifiable);
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
        if (! $manager instanceof VoiceCallManager && ! $manager instanceof Driver) {
            throw new Exception('Invalid data for voice call notification.');
        }
    }
}
