<?php

namespace HoomanMirghasemi\Sms\Tests\Feature\Drivers;

use HoomanMirghasemi\Sms\Drivers\Magfa;
use HoomanMirghasemi\Sms\Models\SmsReport;
use Mockery\MockInterface;
use HoomanMirghasemi\Sms\Tests\TestCase;

class MagfaTest extends TestCase
{
    private string $mobile = '+98935123456';

    public function testFailedConnectToMagfa()
    {
        config()->set('sms.drivers.magfa.wsdl_url', 'wrong url');
        $magfa = $this->app->make(Magfa::class);

        $result = $magfa->to($this->mobile)
            ->message('Test Magfa sms')
            ->send();
        $this->assertFalse($result);
        $this->assertDatabaseHas(SmsReport::class, [
            'mobile' => $this->mobile,
            'success' => false,
            'message' => 'Test Magfa sms',
        ]);
    }

    public function testSuccessSend()
    {
        $magfaStub = $this->partialMock(Magfa::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->once()
                ->andReturnTrue();
        });

        $result = $magfaStub->to($this->mobile)
            ->message('Test Magfa sms')
            ->send();

        $this->assertTrue($result);
    }
}
