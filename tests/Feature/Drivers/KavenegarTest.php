<?php

namespace HoomanMirghasemi\Sms\Tests\Feature\Drivers;

use HoomanMirghasemi\Sms\Drivers\Kavenegar;
use HoomanMirghasemi\Sms\Message;
use HoomanMirghasemi\Sms\Tests\TestCase;
use Mockery\MockInterface;

class KavenegarTest extends TestCase
{
    private string $mobile = '+98935123456';

    public function testSuccessSend()
    {
        $kavenegarStub = $this->partialMock(Kavenegar::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->once()
                ->andReturnTrue();
        });

        $result = $kavenegarStub->to($this->mobile)
            ->message('Test Kavenegar sms')
            ->send();

        $this->assertTrue($result);
    }

    public function testSuccessSendWithMessageTemplate()
    {
        $kavenegarStub = $this->partialMock(Kavenegar::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->once()
                ->andReturnTrue();
        });

        $code = '233269';
        $message = 'کد تایید ارسال شده ';
        $message = new Message($message.PHP_EOL.$code);
        $message->useTemplateIfSupports('SmsRegisterSuccess', [
            'token1' => $code,
        ]);

        $result = $kavenegarStub->to($this->mobile)
            ->message($message)
            ->send();

        $this->assertTrue($result);
    }
}
