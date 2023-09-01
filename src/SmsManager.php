<?php

namespace HoomanMirghasemi\Sms;

use HoomanMirghasemi\Sms\Drivers\FakeSmsSender;
use HoomanMirghasemi\Sms\Drivers\Kavenegar;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Manager;

class SmsManager extends Manager
{
    /**
     * Create an instance of the nexmo sms sender driver.
     *
     * @return Kavenegar
     */
    protected function createKavenegarDriver(): Kavenegar
    {
        return $this->container->make(Kavenegar::class);
    }

    /**
     * Create an instance of the fake sms sender driver.
     *
     * @return FakeSmsSender
     */
    protected function createFakeDriver(): FakeSmsSender
    {
        return $this->container->make(FakeSmsSender::class);
    }

    /**
     * Get the default sms driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        if (App::environment('testing')) {
            $this->setDefaultDriver('fake');
        }

        return $this->config->get('sms.driver');
    }

    /**
     * Set the default sms driver name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultDriver(string $name): void
    {
        $this->config->set('sms.driver', $name);
    }
}
