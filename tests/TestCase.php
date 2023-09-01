<?php

namespace HoomanMirghasemi\Sms\Tests;

use HoomanMirghasemi\Sms\Providers\EventServiceProvider;
use HoomanMirghasemi\Sms\Providers\RouteServiceProvider;
use HoomanMirghasemi\Sms\Providers\SmsProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            SmsProvider::class,
            RouteServiceProvider::class,
            EventServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('app.key', 'base64:TFjZhgLcOs0EA4aDENB7ADGDQvXs/0U8dPEa0S3TRl8=');

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testSmsDb');
        $app['config']->set('database.connections.testSmsDb', [
            'driver'                  => 'sqlite',
            'database'                => ':memory:',
            'prefix'                  => '',
            'foreign_key_constraints' => true,
        ]);

        $app['config']->set('sms.driver', 'fake');
    }
}
