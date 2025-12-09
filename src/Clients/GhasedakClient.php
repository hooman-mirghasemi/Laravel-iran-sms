<?php

namespace HoomanMirghasemi\Sms\Clients;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GhasedakClient
{
    protected PendingRequest $http;

    public function __construct(string $baseApiUrl, string $apiKey)
    {
        $this->http = Http::baseUrl($baseApiUrl)
            ->withHeaders(['ApiKey' => $apiKey])
            ->accept('text/plain')
            ->timeout(20); // 20 seconds wait for response
    }

    /**
     * @throws ConnectionException|RequestException
     */
    public function getAccountInformation(): PromiseInterface|Response
    {
        return $this->http->get('GetAccountInformation');
    }

    /**
     * @throws ConnectionException|RequestException
     */
    public function sendSingleSMS(string $lineNumber, string $receptor, string $message): PromiseInterface|Response
    {
        return $this->http->post('SendSingleSMS', [
            'lineNumber' => $lineNumber,
            'receptor'   => $receptor,
            'message'    => $message,
        ]);
    }

    public function sendOtpWithParams(string $templateName, string $receptor, ...$params): PromiseInterface|Response
    {
        $parameters = [];

        foreach ($params as $index => $param) {
            if (null == $param) {
                continue;
            }
            $parameters['param'.$index + 1] = $param;
        }

        return $this->http->post('SendOtpWithParams', array_merge([
            'receptors' => [[
                'mobile'            => $receptor,
                'clientReferenceId' => "$receptor:".time(),
            ]],
            'templateName' => $templateName,
        ], $parameters));
    }
}
