<?php

namespace HoomanMirghasemi\Sms\Drivers;

use Exception;
use HoomanMirghasemi\Sms\Abstracts\Driver;
use Illuminate\Support\Facades\Log;
use SoapClient;

class SmsOnline extends Driver
{
    private SoapClient $soapClient;

    public function __construct(protected array $settings)
    {
        $this->from = data_get($this->settings, 'from');
        $this->tryConnectToProvider();
    }

    /**
     * Send sms method for OnlineSms.
     *
     * This method send sms and save log to db.
     *
     * @return bool
     */
    public function send(): bool
    {
        if (!$this->serviceActive) {
            parent::failedConnectToProvider();

            return false;
        }
        $response = $this->soapClient->SendSms([
            'username' => data_get($this->settings, 'username'),
            'password' => data_get($this->settings, 'password'), // Credientials
            'from'     => data_get($this->settings, 'from'),
            'to'       => [str_replace('+98', '', $this->recipient)],
            'text'     => $this->getMessage(),
            'isflash'  => false,
            'udh'      => '',
            'recId'    => [0],
            'status'   => [0],
        ]);
        if ($response->SendSmsResult != 1) {
            $this->webserviceResponse = 'An error occured';
            $this->webserviceResponse .= 'Error Code : '.$this->getErrors()[$response->SendSmsResult];
            $this->success = false;
        } else {
            $this->webserviceResponse = 'Message has been successfully sent ; MessageId : '.$response->recId->long;
            $this->success = true;
        }

        return parent::send();
    }

    /**
     * Return the remaining balance of smsonline.
     *
     * @return string
     */
    public function getBalance(): string
    {
        if (!$this->serviceActive) {
            return 'ماژول پیامک آنلاین اس ام اس برنامه غیر فعال می باشد.';
        }

        try {
            $getCreditResult = $this->soapClient->GetCredit([
                'username' => data_get($this->settings, 'username'),
                'password' => data_get($this->settings, 'password'),
            ]);

            return (int) ceil($getCreditResult->GetCreditResult);
        } catch (Exception $e) {
            return 'message:'.$e->getMessage().' code: '.$e->getCode();
        }
    }

    /**
     * Return error messages for SmsMagfa.
     *
     * @return array
     */
    private function getErrors(): array
    {
        $errors = [];
        $errors[0] = 'invalid username or password';
        $errors[1] = 'there is no error, sms successfully sent';
        $errors[2] = 'not enough credit';
        $errors[3] = 'limit in daily send';
        $errors[4] = 'limit in volume send';
        $errors[5] = 'sender number is not correct or invalid';
        $errors[6] = 'there is no correct number for send sms';
        $errors[7] = 'sms content is empty';
        $errors[8] = 'sender user or creator of sender is inactive';
        $errors[9] = 'numbers is to much';
        $errors[100] = 'you are not valid to send';

        return $errors;
    }

    /**
     * Make SoapClient object and connect to magfa wsdl webservices.
     *
     * @return void
     */
    private function tryConnectToProvider(): void
    {
        try {
            $this->soapClient = new SoapClient(data_get($this->settings, 'wsdl_url'));
        } catch (\SoapFault $soapFault) {
            Log::error('onlinesms sms code: '.$soapFault->getCode().' message: '.$soapFault->getMessage());
            $this->serviceActive = false;
        }
    }
}
