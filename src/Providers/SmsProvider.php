<?php

namespace HoomanMirghasemi\Sms\Providers;

use HoomanMirghasemi\Sms\Drivers\Avanak;
use HoomanMirghasemi\Sms\Drivers\FakeSmsSender;
use HoomanMirghasemi\Sms\Drivers\Ghasedak;
use HoomanMirghasemi\Sms\Drivers\Kavenegar;
use HoomanMirghasemi\Sms\Drivers\Magfa;
use HoomanMirghasemi\Sms\Drivers\SmsOnline;
use HoomanMirghasemi\Sms\SmsManager;
use HoomanMirghasemi\Sms\VoiceCallManager;
use Illuminate\Support\ServiceProvider;

class SmsProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrations()->loadViews();
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

        $this->app->bind(Magfa::class, function () {
            $config = config('sms.drivers.magfa') ?? [];

            return new Magfa($config);
        });

        $this->app->bind(SmsOnline::class, function () {
            $config = config('sms.drivers.smsonline') ?? [];

            return new SmsOnline($config);
        });

        $this->app->bind(Avanak::class, function () {
            $config = config('sms.drivers.avanak') ?? [];

            return new Avanak($config);
        });

        if (class_exists(\Kavenegar\KavenegarApi::class)) {
            $this->app->bind(Kavenegar::class, function () {
                $config = config('sms.drivers.kavenegar') ?? [];
                $kavenegarApi = new \Kavenegar\KavenegarApi($config['apiKey']);

                return new Kavenegar($config, $kavenegarApi);
            });
        }

        if (class_exists(\Ghasedak\GhasedaksmsApi::class)) {
            $this->app->bind(Ghasedak::class, function () {
                $config = config('sms.drivers.ghasedak') ?? [];
                $ghasedakApi = new \Ghasedak\GhasedaksmsApi($config['apiKey']);

                return new Ghasedak($config, $ghasedakApi);
            });
        }
    }

    private function loadMigrations(): self
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('/migrations'),
        ], 'iran-sms-migrations');

        return $this;
    }

    /**
     * Register views.
     */
    public function loadViews(): self
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'sms');

        $this->publishes([
            __DIR__.'/../Resources/views' => base_path('resources/views/vendor/sms'),
        ], 'iran-sms-views');

        return $this;
    }

    private function mergeConfigFiles(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/sms.php',
            'sms'
        );

        $this->publishes([
            __DIR__.'/../../config/sms.php' => config_path('sms.php'),
        ], 'iran-sms-config');
    }
}
