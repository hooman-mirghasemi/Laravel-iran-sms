<?php

namespace HoomanMirghasemi\Sms\Tests\Unit\Clients;

use HoomanMirghasemi\Sms\Clients\GhasedakClient;
use HoomanMirghasemi\Sms\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class GhasedakClientTest extends TestCase
{
    private string $baseApiUrl = 'https://gateway.ghasedak.me/rest/api/v1/WebService';
    private string $apiKey = 'test-api-key-123';

    public function testGetAccountInformationSuccess()
    {
        Http::fake([
            'GetAccountInformation' => Http::response([
                'isSuccess' => true,
                'statusCode' => 200,
                'message' => 'Success',
                'data' => [
                    'credit' => '50000'
                ]
            ], 200)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $response = $client->getAccountInformation();

        $this->assertTrue($response->successful());
        $data = $response->json();
        $this->assertTrue($data['isSuccess']);
        $this->assertEquals('50000', $data['data']['credit']);

        Http::assertSent(function ($request) {
            return $request->hasHeader('ApiKey', $this->apiKey) &&
                   $request->url() === $this->baseApiUrl . '/GetAccountInformation';
        });
    }

    public function testGetAccountInformationFailure()
    {
        Http::fake([
            'GetAccountInformation' => Http::response([
                'isSuccess' => false,
                'statusCode' => 401,
                'message' => 'Invalid API key'
            ], 401)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $response = $client->getAccountInformation();

        $this->assertTrue($response->failed());
        $this->assertEquals(401, $response->status());
    }

    public function testSendSingleSMSSuccess()
    {
        Http::fake([
            'SendSingleSMS' => Http::response([
                'isSuccess' => true,
                'statusCode' => 200,
                'message' => 'Message sent successfully',
                'data' => [
                    'lineNumber' => '30005088',
                    'messageId' => '123456789'
                ]
            ], 200)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $response = $client->sendSingleSMS('30005088', '09121234567', 'Test message');

        $this->assertTrue($response->successful());
        $data = $response->json();
        $this->assertTrue($data['isSuccess']);
        $this->assertEquals('30005088', $data['data']['lineNumber']);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $request->hasHeader('ApiKey', $this->apiKey) &&
                   $request->url() === $this->baseApiUrl . '/SendSingleSMS' &&
                   $body['lineNumber'] === '30005088' &&
                   $body['receptor'] === '09121234567' &&
                   $body['message'] === 'Test message';
        });
    }

    public function testSendSingleSMSFailure()
    {
        Http::fake([
            'SendSingleSMS' => Http::response([
                'isSuccess' => false,
                'statusCode' => 400,
                'message' => 'Invalid line number'
            ], 400)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $response = $client->sendSingleSMS('invalid', '09121234567', 'Test');

        $this->assertTrue($response->failed());
        $this->assertEquals(400, $response->status());
    }

    public function testSendOtpWithParamsOneParameter()
    {
        Http::fake([
            'SendOtpWithParams' => Http::response([
                'isSuccess' => true,
                'statusCode' => 200,
                'message' => 'OTP sent successfully'
            ], 200)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $response = $client->sendOtpWithParams('VerificationCode', '09121234567', '123456');

        $this->assertTrue($response->successful());

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $request->url() === $this->baseApiUrl . '/SendOtpWithParams' &&
                   $body['templateName'] === 'VerificationCode' &&
                   $body['receptors'][0]['mobile'] === '09121234567' &&
                   isset($body['param1']) &&
                   $body['param1'] === '123456';
        });
    }

    public function testSendOtpWithParamsMultipleParameters()
    {
        Http::fake([
            'SendOtpWithParams' => Http::response([
                'isSuccess' => true,
                'statusCode' => 200,
                'message' => 'OTP sent successfully'
            ], 200)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $response = $client->sendOtpWithParams(
            'OrderConfirmation',
            '09121234567',
            'John',
            '5000',
            'Order#123'
        );

        $this->assertTrue($response->successful());

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $body['templateName'] === 'OrderConfirmation' &&
                   $body['param1'] === 'John' &&
                   $body['param2'] === '5000' &&
                   $body['param3'] === 'Order#123';
        });
    }

    public function testSendOtpWithParamsNullParameters()
    {
        Http::fake([
            'SendOtpWithParams' => Http::response([
                'isSuccess' => true,
                'statusCode' => 200,
                'message' => 'OTP sent successfully'
            ], 200)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $response = $client->sendOtpWithParams(
            'Template',
            '09121234567',
            'param1',
            null,
            'param3',
            null
        );

        $this->assertTrue($response->successful());

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $body['param1'] === 'param1' &&
                   !isset($body['param2']) &&
                   $body['param3'] === 'param3' &&
                   !isset($body['param4']);
        });
    }

    public function testSendOtpWithParamsAllTenParameters()
    {
        Http::fake([
            'SendOtpWithParams' => Http::response([
                'isSuccess' => true,
                'statusCode' => 200,
                'message' => 'OTP sent successfully'
            ], 200)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $response = $client->sendOtpWithParams(
            'Template',
            '09121234567',
            'p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7', 'p8', 'p9', 'p10'
        );

        $this->assertTrue($response->successful());

        Http::assertSent(function ($request) {
            $body = $request->data();
            for ($i = 1; $i <= 10; $i++) {
                if (!isset($body["param$i"]) || $body["param$i"] !== "p$i") {
                    return false;
                }
            }
            return true;
        });
    }

    public function testConnectionTimeout()
    {
        Http::fake([
            'GetAccountInformation' => Http::response([], 500)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $response = $client->getAccountInformation();

        $this->assertTrue($response->failed());
        $this->assertTrue($response->serverError());
        $this->assertEquals(500, $response->status());
    }

    public function testClientReferenceIdFormat()
    {
        Http::fake([
            'SendOtpWithParams' => Http::response([
                'isSuccess' => true,
                'statusCode' => 200,
                'message' => 'Success'
            ], 200)
        ]);

        $client = new GhasedakClient($this->baseApiUrl, $this->apiKey);
        $client->sendOtpWithParams('Template', '09121234567', 'test');

        Http::assertSent(function ($request) {
            $body = $request->data();
            $clientRefId = $body['receptors'][0]['clientReferenceId'];
            return str_starts_with($clientRefId, '09121234567:');
        });
    }
}
