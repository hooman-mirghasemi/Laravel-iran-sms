<?php

namespace HoomanMirghasemi\Sms\Drivers;

use HoomanMirghasemi\Sms\Abstracts\Driver;
use HoomanMirghasemi\Sms\Clients\GhasedakClient;

class Ghasedak extends Driver
{
    protected string $from = '';

    private GhasedakClient $ghasedakClient;

    public function __construct(
        protected array $settings,
        private ?string $lineNumber = null,
    ) {
        if (!isset($this->lineNumber) && isset($this->settings['from'])) {
            $this->lineNumber = $this->settings['from'];
        }

        $this->ghasedakClient = new GhasedakClient(
            $this->settings['base_api_url'] ?? 'https://gateway.ghasedak.me/rest/api/v1/WebService',
            $this->settings['apiKey'] ?? '',
        );
    }

    public function getBalance(): string
    {
        if (!$this->serviceActive) {
            parent::failedConnectToProvider();

            return '';
        }

        try {
            $response = $this->ghasedakClient->getAccountInformation();

            if ($response->failed()) {
                throw new \Exception('HTTP request failed with status: '.$response->status(), $response->status());
            }

            $accountInfo = $response->json();

            if (!$accountInfo['isSuccess']) {
                throw new \Exception('Ghasedak responded with an error: '.$accountInfo['message'], $accountInfo['statusCode']);
            }

            return $accountInfo['data']['credit'] ?? '';
        } catch (\Exception $e) {
            return 'message:'.$e->getMessage().' code: '.$e->getCode();
        }
    }

    public function send(): bool
    {
        if (!$this->serviceActive) {
            parent::failedConnectToProvider();

            return false;
        }

        try {
            if ($this->message->usesTemplate()) {
                $template = $this->message->getTemplate();
                $identifier = $template['identifier'];
                $params = array_values($template['params']);

                $response = $this->ghasedakClient->sendOtpWithParams(
                    $identifier,
                    $this->recipient,
                    $params[0],
                    $params[1] ?? null,
                    $params[2] ?? null,
                    $params[3] ?? null,
                    $params[4] ?? null,
                    $params[5] ?? null,
                    $params[6] ?? null,
                    $params[7] ?? null,
                    $params[8] ?? null,
                    $params[9] ?? null,
                );

                if ($response->failed()) {
                    throw new \Exception('HTTP request failed with status: '.$response->status(), $response->status());
                }

                $result = $response->json();

                if (!$result['isSuccess']) {
                    throw new \Exception('Ghasedak responded with an error: '.$result['message'], $result['statusCode']);
                }

                $this->success = true;
                $this->from = $identifier;
                $this->message = $result['message'];
            } else {
                $response = $this->ghasedakClient->sendSingleSMS(
                    $this->lineNumber,
                    $this->recipient,
                    $this->message->toString(),
                );

                if ($response->failed()) {
                    throw new \Exception('HTTP request failed with status: '.$response->status(), $response->status());
                }

                $result = $response->json();

                if (!$result['isSuccess']) {
                    throw new \Exception('Ghasedak responded with an error: '.$result['message'], $result['statusCode']);
                }

                $this->success = true;
                $this->from = $result['data']['lineNumber'];
                $this->message = $result['message'];
            }

            $this->webserviceResponse = json_encode($result);
        } catch (\Exception $exception) {
            $this->webserviceResponse = $exception->getMessage();
            $this->success = false;
        }

        return parent::send();
    }
}
