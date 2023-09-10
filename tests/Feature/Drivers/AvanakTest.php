<?php

namespace HoomanMirghasemi\Sms\Tests\Feature\Drivers;

use HoomanMirghasemi\Sms\Drivers\Avanak;
use HoomanMirghasemi\Sms\Models\SmsReport;
use HoomanMirghasemi\Sms\Tests\TestCase;
use Mockery\MockInterface;

class AvanakTest extends TestCase
{
    private string $mobile = '+98935123456';

    public function testFailedConnectToAvanak()
    {
        config()->set('sms.drivers.avanak.wsdl_url', 'wrong url');
        $avanak = $this->app->make(Avanak::class);

        $result = $avanak->to($this->mobile)
            ->message('Test Avanak voice call')
            ->send();
        $this->assertFalse($result);
        $this->assertDatabaseHas(SmsReport::class, [
            'mobile'  => $this->mobile,
            'success' => false,
            'message' => 'Test Avanak voice call',
        ]);
    }

    public function testSuccessSend()
    {
        $avanakStub = $this->partialMock(Avanak::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->once()
                ->andReturnTrue();
        });

        $result = $avanakStub->to($this->mobile)
            ->message('Test Avanak voice call')
            ->send();

        $this->assertTrue($result);
    }

    public function testGetBalanceSuccess()
    {
        $avanakStub = $this->partialMock(Avanak::class, function (MockInterface $mock) {
            $mock->shouldReceive('getBalance')
                ->once()
                ->andReturn(250);
        });

        $result = $avanakStub->getBalance();

        $this->assertEquals($result, 250);
    }
}
