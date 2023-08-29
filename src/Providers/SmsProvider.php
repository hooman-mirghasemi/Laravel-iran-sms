<?php

namespace HoomanMirghasemi\Sms\Providers;

use Illuminate\Support\ServiceProvider;
use Kavenegar\KavenegarApi;
use HoomanMirghasemi\Sms\Drivers\FakeSmsSender;
use HoomanMirghasemi\Sms\Drivers\Kavenegar;
use HoomanMirghasemi\Sms\SmsManager;
use HoomanMirghasemi\Sms\VoiceCallManager;

class SmsProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrations()
            ->loadViews();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFiles();

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        /**
         * Bind to service container.
         */
        $this->app->singleton('sms', SmsManager::class);
        $this->app->singleton('voiceCall', VoiceCallManager::class);

        $this->app->bind(FakeSmsSender::class, function () {
            $config = config('sms.drivers.fake') ?? [];

            return new FakeSmsSender($config);
        });

        $this->app->bind(Kavenegar::class, function () {
            $config = config('sms.drivers.kavenegar') ?? [];
            $kavenegarApi = new KavenegarApi($config['apiKey']);

            return new Kavenegar($config, $kavenegarApi);
        });
    }

    private function loadMigrations(): self
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        return $this;
    }

    /**
     * Register views.
     */
    public function loadViews(): self
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'sms');

        return $this;
    }

    private function mergeConfigFiles(): self
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/sms.php', 'sms'
        );

        return $this;
    }
}
