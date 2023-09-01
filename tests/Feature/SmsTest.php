<?php

namespace HoomanMirghasemi\Sms\Tests\Feature;

use HoomanMirghasemi\Sms\Drivers\FakeSmsSender;
use HoomanMirghasemi\Sms\Drivers\Kavenegar;
use HoomanMirghasemi\Sms\Models\SmsReport;
use HoomanMirghasemi\Sms\SmsManager;
use HoomanMirghasemi\Sms\Tests\TestCase;
use Mockery\MockInterface;

class SmsTest extends TestCase
{
    private string $mobile = '+989354223736';

    public function testSendingSmsCheckSaveLogs()
    {
        $sms = resolve(SmsManager::class);
        FakeSmsSender::$successSend = true;
        $sms->driver('fake')
            ->to($this->mobile)
            ->message('Test successful send sms.')
            ->send();
        FakeSmsSender::$successSend = false;
        $sms->driver('fake')
            ->to($this->mobile)
            ->message('Test failed send sms.')
            ->send();
        $this->assertDatabaseCount(SmsReport::class, 2);
        $this->assertDatabaseHas(SmsReport::class, [
            'mobile'  => $this->mobile,
            'success' => true,
            'message' => 'Test successful send sms.',
            'from'    => 'fakesmssender',
        ]);
        $this->assertDatabaseHas(SmsReport::class, [
            'mobile'  => $this->mobile,
            'success' => false,
            'message' => 'Test failed send sms.',
            'from'    => 'fakesmssender',
        ]);
    }

    public function testCallBackWork()
    {
        FakeSmsSender::$successSend = true;
        $sms = resolve(SmsManager::class);
        $testValueVariable = 'before';
        $sms->driver('fake')
            ->to($this->mobile)
            ->message('test sms')
            ->setCallBack(function ($result) use (&$testValueVariable) {
                if ($result) {
                    $testValueVariable = 'sms send successfully';
                }
            })
            ->send();
        $this->assertEquals('sms send successfully', $testValueVariable);
    }

    public function testKavenegarSuccessSend()
    {
        $kavenegarStub = $this->partialMock(Kavenegar::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')->once()->andReturn(true);
        });

        $this->instance(Kavenegar::class, $kavenegarStub);

        $sms = resolve(SmsManager::class);
        $sms->driver('kavenegar')
            ->to($this->mobile)
            ->message('Test kavenegar sms')
            ->send();
    }
}
