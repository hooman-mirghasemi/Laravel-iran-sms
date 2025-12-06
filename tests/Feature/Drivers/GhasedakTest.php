<?php

namespace HoomanMirghasemi\Sms\Tests\Feature\Drivers;

use HoomanMirghasemi\Sms\Drivers\Ghasedak;
use HoomanMirghasemi\Sms\Message;
use HoomanMirghasemi\Sms\Tests\TestCase;
use Mockery\MockInterface;

class GhasedakTest extends TestCase
{
    private string $mobile = '+98935123456';

    public function testSuccessSend()
    {
        /** @var Ghasedak $ghasedakStub */
        $ghasedakStub = $this->partialMock(Ghasedak::class, function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->once()
                ->andReturnTrue();
        });

        $result = $ghasedakStub->to($this->mobile)
            ->message('Test ghasedak sms')
            ->send();

        $this->assertTrue($result);
    }

    public function testSuccessSendWithMessageTemplate()
    {
        /** @var Ghasedak $ghasedakStub */
        $ghasedakStub = $this->partialMock(Ghasedak::class, function (MockInterface $mock) {
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

        $result = $ghasedakStub->to($this->mobile)
            ->message($message)
            ->send();

        $this->assertTrue($result);
    }
}
