<?php

namespace HoomanMirghasemi\Sms\Channels;

use Exception;
use HoomanMirghasemi\Sms\Abstracts\Driver;
use HoomanMirghasemi\Sms\VoiceCallManager;
use Illuminate\Notifications\Notification;

class VoiceCallChannel
{
    /**
     * Send notification.
     *
     * @param              $notifiable
     * @param Notification $notification
     *
     * @throws Exception
     *
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        $manager = $notification->toVoiceCall($notifiable);
        if (!is_null($manager)) {
            $this->validate($manager);
            $manager->send();
        }
    }

    /**
     * Validate sms.
     *
     * @throws Exception
     *
     * @return void
     */
    protected function validate($manager): void
    {
        if (!$manager instanceof VoiceCallManager && !$manager instanceof Driver) {
            throw new Exception('Invalid data for voice call notification.');
        }
    }
}
