<?php

namespace HoomanMirghasemi\Sms;

use HoomanMirghasemi\Sms\Drivers\Avanak;
use HoomanMirghasemi\Sms\Drivers\FakeSmsSender;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Manager;

class VoiceCallManager extends Manager
{
    /**
     * Create an instance of the avanak voice call sender driver.
     *
     * @return Avanak
     */
    protected function createAvanakDriver(): Avanak
    {
        return $this->container->make(Avanak::class);
    }

    /**
     * Create an instance of the fake voice call sender driver.
     *
     * @return FakeSmsSender
     */
    protected function createFakeDriver(): FakeSmsSender
    {
        return $this->container->make(FakeSmsSender::class);
    }

    /**
     * Get the default voice call driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        if (App::environment('testing')) {
            $this->setDefaultDriver('fake');
        }

        return $this->config->get('sms.driver_voice_call');
    }

    /**
     * Set the default sms driver name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultDriver($name): void
    {
        $this->config->set('sms.driver_voice_call', $name);
    }
}
