<?php

namespace HoomanMirghasemi\Sms\Tests\Feature\Drivers;

use HoomanMirghasemi\Sms\Drivers\Ghasedak;
use HoomanMirghasemi\Sms\Events\ProviderConnectionFailedEvent;
use HoomanMirghasemi\Sms\Events\SmsSentEvent;
use HoomanMirghasemi\Sms\Message;
use HoomanMirghasemi\Sms\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

class GhasedakTest extends TestCase
{
    private string $mobile = '09351234567';
    private string $lineNumber = '30005088';
    private array $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'base_api_url' => 'https://gateway.ghasedak.me/rest/api/v1/WebService',
            'apiKey'       => 'test-api-key',
            'from'         => $this->lineNumber,
        ];
    }

    public function testSuccessSendSingleSMS()
    {
        Event::fake();

        Http::fake([
            '*/SendSingleSMS' => Http::response([
                'isSuccess'  => true,
                'statusCode' => 200,
                'message'    => 'Message sent successfully',
                'data'       => [
                    'lineNumber' => $this->lineNumber,
                    'messageId'  => '123456789',
                ],
            ], 200),
        ]);

        $ghasedak = new Ghasedak($this->config);
        $result = $ghasedak->to($this->mobile)
            ->message('Test ghasedak sms')
            ->send();

        $this->assertTrue($result);
        $this->assertEquals($this->lineNumber, $ghasedak->getSenderNumber());
        $this->assertEquals($this->mobile, $ghasedak->getRecipient());
        $this->assertTrue($ghasedak->getResult());
        $this->assertStringContainsString('Message sent successfully', $ghasedak->getWebServiceResponce());

        Event::assertDispatched(SmsSentEvent::class);
    }

    public function testSuccessSendWithMessageTemplate()
    {
        Event::fake();

        Http::fake([
            '*/SendOtpWithParams' => Http::response([
                'isSuccess'  => true,
                'statusCode' => 200,
                'message'    => 'OTP sent successfully',
            ], 200),
        ]);

        $code = '233269';
        $message = new Message('کد تایید ارسال شده '.PHP_EOL.$code);
        $message->useTemplateIfSupports('SmsRegisterSuccess', [
            'token1' => $code,
        ]);

        $ghasedak = new Ghasedak($this->config);
        $result = $ghasedak->to($this->mobile)
            ->message($message)
            ->send();

        $this->assertTrue($result);
        $this->assertEquals('SmsRegisterSuccess', $ghasedak->getSenderNumber());
        $this->assertTrue($ghasedak->getResult());

        Event::assertDispatched(SmsSentEvent::class);
    }

    public function testSendFailureInvalidApiKey()
    {
        Event::fake();

        Http::fake([
            '*/SendSingleSMS' => Http::response([
                'isSuccess'  => false,
                'statusCode' => 401,
                'message'    => 'Invalid API key',
            ], 401),
        ]);

        $ghasedak = new Ghasedak($this->config);
        $result = $ghasedak->to($this->mobile)
            ->message('Test message')
            ->send();

        $this->assertFalse($result);
        $this->assertFalse($ghasedak->getResult());
        $this->assertStringContainsString('HTTP request failed with status: 401', $ghasedak->getWebServiceResponce());

        Event::assertDispatched(SmsSentEvent::class);
    }

    public function testSendSingleSMSFailureWithSuccessfulHttpButFailedResponse()
    {
        Event::fake();

        Http::fake([
            '*/SendSingleSMS' => Http::response([
                'isSuccess'  => false,
                'statusCode' => 400,
                'message'    => 'Insufficient credit',
            ], 200),
        ]);

        $ghasedak = new Ghasedak($this->config);
        $result = $ghasedak->to($this->mobile)
            ->message('Test message')
            ->send();

        $this->assertFalse($result);
        $this->assertFalse($ghasedak->getResult());
        $this->assertStringContainsString('Insufficient credit', $ghasedak->getWebServiceResponce());

        Event::assertDispatched(SmsSentEvent::class);
    }

    public function testSendFailureNetworkError()
    {
        Event::fake();

        Http::fake([
            '*/SendSingleSMS' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Network error');
            },
        ]);

        $ghasedak = new Ghasedak($this->config);
        $result = $ghasedak->to($this->mobile)
            ->message('Test message')
            ->send();

        $this->assertFalse($result);
        $this->assertFalse($ghasedak->getResult());
        $this->assertStringContainsString('Network error', $ghasedak->getWebServiceResponce());

        Event::assertDispatched(SmsSentEvent::class);
    }

    public function testGetBalanceSuccess()
    {
        Http::fake([
            '*/GetAccountInformation' => Http::response([
                'isSuccess'  => true,
                'statusCode' => 200,
                'message'    => 'Success',
                'data'       => [
                    'credit' => '50000',
                ],
            ], 200),
        ]);

        $ghasedak = new Ghasedak($this->config);
        $balance = $ghasedak->getBalance();

        $this->assertEquals('50000', $balance);
    }

    public function testGetBalanceFailure()
    {
        Http::fake([
            '*/GetAccountInformation' => Http::response([
                'isSuccess'  => false,
                'statusCode' => 401,
                'message'    => 'Unauthorized',
            ], 401),
        ]);

        $ghasedak = new Ghasedak($this->config);
        $balance = $ghasedak->getBalance();

        $this->assertStringContainsString('HTTP request failed with status: 401', $balance);
        $this->assertStringContainsString('message:', $balance);
    }

    public function testGetBalanceFailureWithSuccessfulHttpButFailedResponse()
    {
        Http::fake([
            '*/GetAccountInformation' => Http::response([
                'isSuccess'  => false,
                'statusCode' => 403,
                'message'    => 'Access denied',
            ], 200),
        ]);

        $ghasedak = new Ghasedak($this->config);
        $balance = $ghasedak->getBalance();

        $this->assertStringContainsString('Access denied', $balance);
        $this->assertStringContainsString('message:', $balance);
        $this->assertStringContainsString('code:', $balance);
    }

    public function testGetBalanceNetworkError()
    {
        Http::fake([
            '*/GetAccountInformation' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection failed');
            },
        ]);

        $ghasedak = new Ghasedak($this->config);
        $balance = $ghasedak->getBalance();

        $this->assertStringContainsString('Connection failed', $balance);
        $this->assertStringContainsString('message:', $balance);
    }

    public function testGetBalanceWhenServiceInactive()
    {
        Event::fake();

        $ghasedak = new Ghasedak($this->config);

        // Use reflection to set serviceActive to false
        $reflection = new \ReflectionClass($ghasedak);
        $property = $reflection->getProperty('serviceActive');
        $property->setAccessible(true);
        $property->setValue($ghasedak, false);

        $balance = $ghasedak->getBalance();

        $this->assertEquals('', $balance);
        Event::assertDispatched(ProviderConnectionFailedEvent::class);
    }

    public function testSendWhenServiceInactive()
    {
        Event::fake();

        $ghasedak = new Ghasedak($this->config);

        // Use reflection to set serviceActive to false
        $reflection = new \ReflectionClass($ghasedak);
        $property = $reflection->getProperty('serviceActive');
        $property->setAccessible(true);
        $property->setValue($ghasedak, false);

        $result = $ghasedak->to($this->mobile)
            ->message('Test')
            ->send();

        $this->assertFalse($result);
        Event::assertDispatched(ProviderConnectionFailedEvent::class);
    }

    public function testConstructorWithoutLineNumber()
    {
        $config = [
            'base_api_url' => 'https://gateway.ghasedak.me/rest/api/v1/WebService',
            'apiKey'       => 'test-api-key',
        ];

        $ghasedak = new Ghasedak($config);

        // LineNumber should be null when not provided
        $reflection = new \ReflectionClass($ghasedak);
        $property = $reflection->getProperty('lineNumber');
        $property->setAccessible(true);

        $this->assertNull($property->getValue($ghasedak));
    }

    public function testConstructorWithExplicitLineNumber()
    {
        $customLineNumber = '30001234';
        $ghasedak = new Ghasedak($this->config, $customLineNumber);

        $reflection = new \ReflectionClass($ghasedak);
        $property = $reflection->getProperty('lineNumber');
        $property->setAccessible(true);

        $this->assertEquals($customLineNumber, $property->getValue($ghasedak));
    }

    public function testSendWithMultipleTemplateParams()
    {
        Event::fake();

        Http::fake([
            '*/SendOtpWithParams' => Http::response([
                'isSuccess'  => true,
                'statusCode' => 200,
                'message'    => 'OTP sent successfully',
            ], 200),
        ]);

        $message = new Message('Test message');
        $message->useTemplateIfSupports('OrderTemplate', [
            'token1' => 'John',
            'token2' => '5000',
            'token3' => 'Order#123',
        ]);

        $ghasedak = new Ghasedak($this->config);
        $result = $ghasedak->to($this->mobile)
            ->message($message)
            ->send();

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return 'OrderTemplate' === $body['templateName']
                   && 'John' === $body['param1']
                   && '5000' === $body['param2']
                   && 'Order#123' === $body['param3'];
        });
    }

    public function testSendOtpTemplateFailure()
    {
        Event::fake();

        Http::fake([
            '*/SendOtpWithParams' => Http::response([
                'isSuccess'  => false,
                'statusCode' => 400,
                'message'    => 'Invalid template',
            ], 400),
        ]);

        $message = new Message('Code: 123456');
        $message->useTemplateIfSupports('InvalidTemplate', [
            'token1' => '123456',
        ]);

        $ghasedak = new Ghasedak($this->config);
        $result = $ghasedak->to($this->mobile)
            ->message($message)
            ->send();

        $this->assertFalse($result);
        $this->assertStringContainsString('HTTP request failed with status: 400', $ghasedak->getWebServiceResponce());
    }

    public function testSendOtpTemplateFailureWithSuccessfulHttpButFailedResponse()
    {
        Event::fake();

        Http::fake([
            '*/SendOtpWithParams' => Http::response([
                'isSuccess'  => false,
                'statusCode' => 404,
                'message'    => 'Template not found',
            ], 200),
        ]);

        $message = new Message('Code: 123456');
        $message->useTemplateIfSupports('NonExistentTemplate', [
            'token1' => '123456',
        ]);

        $ghasedak = new Ghasedak($this->config);
        $result = $ghasedak->to($this->mobile)
            ->message($message)
            ->send();

        $this->assertFalse($result);
        $this->assertFalse($ghasedak->getResult());
        $this->assertStringContainsString('Template not found', $ghasedak->getWebServiceResponce());

        Event::assertDispatched(SmsSentEvent::class);
    }

    public function testCustomBaseApiUrl()
    {
        $customConfig = [
            'base_api_url' => 'https://custom-api.example.com/api/v1',
            'apiKey'       => 'custom-key',
            'from'         => $this->lineNumber,
        ];

        Http::fake([
            'https://custom-api.example.com/api/v1/SendSingleSMS' => Http::response([
                'isSuccess'  => true,
                'statusCode' => 200,
                'message'    => 'Success',
                'data'       => [
                    'lineNumber' => $this->lineNumber,
                    'messageId'  => '999',
                ],
            ], 200),
        ]);

        $ghasedak = new Ghasedak($customConfig);
        $result = $ghasedak->to($this->mobile)
            ->message('Test')
            ->send();

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'custom-api.example.com');
        });
    }
}
