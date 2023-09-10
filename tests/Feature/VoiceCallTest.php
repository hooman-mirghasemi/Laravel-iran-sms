<?php

namespace HoomanMirghasemi\Sms\Tests\Feature;

use HoomanMirghasemi\Sms\Drivers\FakeSmsSender;
use HoomanMirghasemi\Sms\Models\SmsReport;
use HoomanMirghasemi\Sms\Tests\TestCase;
use HoomanMirghasemi\Sms\VoiceCallManager;

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
            'mobile'  => '+9800',
            'success' => 1,
            'message' => 'Test successful send voice call.',
        ]);
        $this->assertDatabaseHas(SmsReport::class, [
            'mobile'  => '+9800',
            'success' => 0,
            'message' => 'Test failed send voice call.',
        ]);
    }
}
