<?php

namespace HoomanMirghasemi\Sms\Drivers;

use Exception;
use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapFault;
use HoomanMirghasemi\Sms\Abstracts\Driver;

class Avanak extends Driver
{
    private SoapClient $client;

    private array $params;

    public function __construct(protected array $settings)
    {
        $this->from = data_get($this->settings, 'from');

        $this->params['userName'] = data_get($this->settings, 'username');
        $this->params['password'] = data_get($this->settings, 'password');
        $this->params['vote'] = false;
        $this->params['serverid'] = $this->from;

        $this->tryConnectToProvider();
    }

    /**
     * Send voice call method for Avanak.
     *
     * This method send sms and save log to db.
     *
     * @return bool
     */
    public function send(): bool
    {
        if (! $this->serviceActive) {
            parent::failedConnectToProvider();

            return false;
        }
        $this->params['text'] = $this->getMessage();
        $this->params['number'] = $this->recipient;
        if (! str_starts_with($this->params['number'], '+98')) {
            return false;
        }
        // for sending to avanak change number format
        $this->params['number'] = str_replace('+98', '0', $this->params['number']);

        try {
            $response = $this->client->QuickSendWithTTS($this->params);
            if ($response->QuickSendWithTTSResult > 0) {
                $this->webserviceResponse = 'Send message id :'.$response->QuickSendWithTTSResult;
                $this->success = true;
            } else {
                $this->webserviceResponse = 'Error Code : '.$response->QuickSendWithTTSResult;
                $this->success = false;
            }
        } catch (Exception $e) {
            $this->webserviceResponse = 'code:'.$e->getCode().' message: '.$e->getMessage();
            $this->success = false;
        }

        return parent::send();
    }

    /**
     * Return the remaining balance of avanak.
     *
     * @return string
     */
    public function getBalance(): string
    {
        if (! $this->serviceActive) {
            return 'وب سرویس آوانک با مشکل مواجه شده.';
        }

        try {
            $response = $this->client->GetCredit($this->params);

            return $response->GetCreditResult;
        } catch (Exception $e) {
            return 'message:'.$e->getMessage().' code: '.$e->getCode();
        }
    }

    /**
     * Make SoapClient object and connect to avanak wsdl webservices.
     *
     * @return void
     */
    private function tryConnectToProvider(): void
    {
        try {
            $this->client = new SoapClient(data_get($this->settings, 'wsdl_url'), ['trace' => 1, 'encoding' => 'UTF-8']);
        } catch (SoapFault $soapFault) {
            Log::error('avanak voice call code: '.$soapFault->getCode().' message: '.$soapFault->getMessage());
            $this->serviceActive = false;
        }
    }
}
