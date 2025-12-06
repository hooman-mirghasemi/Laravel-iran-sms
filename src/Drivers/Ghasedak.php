<?php

namespace HoomanMirghasemi\Sms\Drivers;

use DateTimeImmutable;
use Exception;
use Ghasedak\DataTransferObjects\Request\OtpMessageWithParamsDTO;
use Ghasedak\DataTransferObjects\Request\ReceptorDTO;
use Ghasedak\DataTransferObjects\Request\SingleMessageDTO;
use Ghasedak\GhasedaksmsApi;
use HoomanMirghasemi\Sms\Abstracts\Driver;

class Ghasedak extends Driver
{
    protected string $from = '';

    public function __construct(
        protected array $settings,
        private GhasedaksmsApi $ghasedakSmsApi,
        private ?string $lineNumber = null
    )
    {
        if (!isset($this->lineNumber) && isset($this->settings['from'])) {
            $this->lineNumber = $this->settings['from'];
        }
    }

    public function getBalance(): string
    {
        return '';
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

                $result = $this->ghasedakSmsApi->sendOtpWithParams(
                    new OtpMessageWithParamsDTO(
                        new DateTimeImmutable('now'),
                        [new ReceptorDTO($this->recipient, '1')],
                        $identifier,
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
                    )
                );

                $this->success = isset($result);
                $this->from = '';
                $this->message = $identifier;

            } else {
                $result = $this->ghasedakSmsApi->sendSingle(
                    new SingleMessageDTO(
                        new DateTimeImmutable('now'),
                        $this->lineNumber,
                        $this->recipient,
                        $this->message->toString()
                    )
                );

                $this->success = $result->getMessageId() !== null;
                $this->from = $result->getLineNumber();
                $this->message = $result->getMessage();
            }

            $this->webserviceResponse = print_r($result, true);

        } catch (Exception $exception) {
            $this->webserviceResponse = $exception->getMessage();
            $this->success = false;
        }

        return parent::send();
    }
}
