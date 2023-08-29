<?php

namespace HoomanMirghasemi\Sms\Drivers;

use Kavenegar\KavenegarApi;
use HoomanMirghasemi\Sms\Abstracts\Driver;

class Kavenegar extends Driver
{
    protected string $from = '';

    public function __construct(protected array $settings, private KavenegarApi $kavenegarApi)
    {
    }

    public function getBalance(): string
    {
        return '';
    }

    public function send(): bool
    {
        try {
            if ($this->message->usesTemplate()) {
                $template = $this->message->getTemplate();
                $identifier = $template['identifier'];
                $params = $template['params'];
                $token1 = str_replace(' ', '-', $params['token1'] ?? '');
                $token2 = str_replace(' ', '-', $params['token2'] ?? '');
                $token3 = str_replace(' ', '-', $params['token3'] ?? '');

                $token10 = $this->replaceExtraSpace($params['token10'] ?? '', 4);
                $token20 = $this->replaceExtraSpace($params['token20'] ?? '', 8);

                $result = $this->kavenegarApi->VerifyLookup(
                    $this->recipient,
                    $token1,
                    $token2,
                    $token3,
                    $identifier,
                    null,
                    $token10,
                    $token20

                );
            } else {
                $result = $this->kavenegarApi->Send(null, $this->recipient, $this->message->toString());
            }

            $this->success = true;
            $this->from = $result[0]->sender;
            $this->message = $result[0]->message;
            $this->webserviceResponse = print_r($result, true);
        } catch (\Exception $exception) {
            $this->webserviceResponse = $exception->getMessage();
            $this->success = false;
        }

        return parent::send();
    }

    private function replaceExtraSpace($string, $maxSpace)
    {
        $spaceCount = substr_count($string, ' ');
        if ($spaceCount > $maxSpace) {
            $string = strrev(preg_replace('/ /', '-', strrev($string), $spaceCount - $maxSpace));
        }
        return $string;
    }
}
