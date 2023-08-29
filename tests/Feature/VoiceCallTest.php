<?php

namespace HoomanMirghasemi\Sms\Tests\Feature;

use HoomanMirghasemi\Sms\Drivers\FakeSmsSender;
use HoomanMirghasemi\Sms\Models\SmsReport;
use HoomanMirghasemi\Sms\VoiceCallManager;
use HoomanMirghasemi\Sms\Tests\TestCase;

class VoiceCallTest extends TestCase
{
    public function testSendingVoiceCallCheckSaveLogs()
    {
        $voiceCall = resolve(VoiceCallManager::class);
        FakeSmsSender::$successSend = true;
        $voiceCall->driver('fake')
            ->to('+9800')
            ->message('Test successful send voice call.')
            ->send();
        FakeSmsSender::$successSend = false;
        $voiceCall->driver('fake')
            ->to('+9800')
            ->message('Test failed send voice call.')
            ->send();
        $this->assertDatabaseCount(SmsReport::class, 2);
        $this->assertDatabaseHas(SmsReport::class, [
            'mobile' => '+9800',
            'success' => 1,
            'message' => 'Test successful send voice call.',
        ]);
        $this->assertDatabaseHas(SmsReport::class, [
            'mobile' => '+9800',
            'success' => 0,
            'message' => 'Test failed send voice call.',
        ]);
    }

    public function testCallBackWork()
    {
        FakeSmsSender::$successSend = true;
        $sms = resolve(VoiceCallManager::class);
        $testValueVariable = 'before';
        $sms->driver('fake')
            ->to('+9800')
            ->message('test voice call.')
            ->setCallBack(function ($result) use (&$testValueVariable) {
                if ($result) {
                    $testValueVariable = 'voice call send successfully';
                }
            })
            ->send();
        $this->assertEquals('voice call send successfully', $testValueVariable);
    }
}
