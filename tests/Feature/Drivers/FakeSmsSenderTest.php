<?php

namespace HoomanMirghasemi\Sms\Tests\Feature\Drivers;

use HoomanMirghasemi\Sms\Drivers\FakeSmsSender;
use HoomanMirghasemi\Sms\Models\SmsReport;
use HoomanMirghasemi\Sms\Tests\TestCase;

class FakeSmsSenderTest extends TestCase
{
    private string $mobile = '+989354223736';

    public function testFailedSendSms()
    {
        FakeSmsSender::$successSend = false;

        $fakeSmsSender = $this->app->make(FakeSmsSender::class);
        $result = $fakeSmsSender->to($this->mobile)
            ->message('Test fake sms sender')
            ->send();
        $this->assertFalse($result);

        $this->assertDatabaseHas(SmsReport::class, [
            'mobile' => $this->mobile,
            'success' => false,
            'message' => 'Test fake sms sender',
            'from' => 'fakesmssender',
        ]);
    }

    public function testSuccessSend()
    {
        FakeSmsSender::$successSend = true;

        $fakeSmsSender = $this->app->make(FakeSmsSender::class);
        $result = $fakeSmsSender->to($this->mobile)
            ->message('Test fake sms sender')
            ->send();
        $this->assertTrue($result);

        $this->assertDatabaseHas(SmsReport::class, [
            'mobile' => $this->mobile,
            'success' => true,
            'message' => 'Test fake sms sender',
            'from' => 'fakesmssender',
        ]);
    }
}
