<?php

namespace HoomanMirghasemi\Sms\Tests\Feature\Drivers;

use HoomanMirghasemi\Sms\Drivers\SmsOnline;
use HoomanMirghasemi\Sms\Models\SmsReport;
use HoomanMirghasemi\Sms\Tests\TestCase;
use Mockery\MockInterface;

class SmsOnlineTest extends TestCase
{
    private string $mobile = '+98935123456';

    public function testFailedConnectToSmsonline()
    {
        config()->set('sms.drivers.smsonline.wsdl_url', 'wrong url');
        $smsOnline = $this->app->make(SmsOnline::class);

        $result = $smsOnline->to($this->mobile)
            ->message('Test smsOnline sms')
            ->send();
        $this->assertFalse($result);
        $this->assertDatabaseHas(SmsReport::class, [
            'mobile'  => $this->mobile,
            'success' => false,
            'message' => 'Test smsOnline sms',
        ]);
    }

    public function testSuccessSend()
    {
        $smsOnlineStub = $this->partialMock(SmsOnline::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->once()
                ->andReturnTrue();
        });

        $result = $smsOnlineStub->to($this->mobile)
            ->message('Test smsOnline sms')
            ->send();

        $this->assertTrue($result);
    }
}
