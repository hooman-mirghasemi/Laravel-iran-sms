<?php

namespace HoomanMirghasemi\Sms\Listeners;

use HoomanMirghasemi\Sms\Abstracts\Driver;
use HoomanMirghasemi\Sms\Contracts\SmsEvent;
use HoomanMirghasemi\Sms\Models\SmsReport;

class DbLogListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(SmsEvent $event): void
    {
        $this->saveLogInDb($event->smsDriver);
    }

    /**
     * This method should call in all driver send method in last line.
     *
     * @return void
     */
    protected function saveLogInDb(Driver $smsDriver): void
    {
        $sendSmsReport = new SmsReport();
        $sendSmsReport->mobile = $smsDriver->getRecipient();
        $sendSmsReport->message = $smsDriver->getMessage();
        $reflect = new \ReflectionClass($smsDriver);
        $sendSmsReport->from = strtolower($reflect->getShortName());
        $sendSmsReport->number = $smsDriver->getSenderNumber();
        $sendSmsReport->web_service_response = $smsDriver->getWebServiceResponce();
        $sendSmsReport->success = $smsDriver->getResult();
        $sendSmsReport->save();
    }
}
