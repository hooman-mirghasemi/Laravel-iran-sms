<?php

namespace HoomanMirghasemi\Sms;

use HoomanMirghasemi\Sms\Drivers\FakeSmsSender;
use HoomanMirghasemi\Sms\Drivers\Ghasedak;
use HoomanMirghasemi\Sms\Drivers\Kavenegar;
use HoomanMirghasemi\Sms\Drivers\Magfa;
use HoomanMirghasemi\Sms\Drivers\SmsOnline;
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
     * Create an instance of the smsOnline sms sender driver.
     *
     * @return SmsOnline
     */
    protected function createSmsOnlineDriver()
    {
        return $this->container->make(SmsOnline::class);
    }

    /**
     * Create an instance of the magfa sms sender driver.
     *
     * @return Magfa
     */
    protected function createMagfaDriver()
    {
        return $this->container->make(Magfa::class);
    }

    /**
     * Create an instance of the ghasedak sms sender driver.
     *
     * @return Ghasedak
     */
    protected function createGhasedakDriver(): Ghasedak
    {
        return $this->container->make(Ghasedak::class);
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
