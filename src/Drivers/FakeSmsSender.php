<?php

namespace HoomanMirghasemi\Sms\Drivers;

use HoomanMirghasemi\Sms\Abstracts\Driver;

class FakeSmsSender extends Driver
{
    /**
     * The Faker sms send sms success of fail.
     *
     * @var bool
     */
    public static bool $successSend = true;

    public function __construct(protected array $settings)
    {
        $this->from = data_get($this->settings, 'from');
    }

    /**
     * Send sms method for Magfa.
     *
     * This method send sms and save log to db.
     *
     * @return bool
     */
    public function send(): bool
    {
        if (!self::$successSend) {
            $this->webserviceResponse = 'An error happened !';
            $this->success = false;
        } else {
            $this->webserviceResponse = 'Message has been successfully sent ; MessageId : '.rand(1, 1000000);
            $this->success = true;
        }

        return parent::send();
    }

    /**
     * Return fake balance :D.
     *
     * @return string
     */
    public function getBalance(): string
    {
        return rand(0, 250000);
    }
}
