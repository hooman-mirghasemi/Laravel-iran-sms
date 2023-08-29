<?php

namespace HoomanMirghasemi\Sms\Abstracts;

use Illuminate\Support\Facades\Event;
use HoomanMirghasemi\Sms\Contracts\Driver as DriverContract;
use HoomanMirghasemi\Sms\Contracts\Message;
use HoomanMirghasemi\Sms\Events\ProviderConnectionFailedEvent;
use HoomanMirghasemi\Sms\Events\SmsSentEvent;

abstract class Driver implements DriverContract
{
    /**
     * If it can not connect to provider this get false value.
     *
     * @var bool
     */
    protected bool $serviceActive = true;

    /**
     * Recipient (mobile number in E164 format)
     *
     * @param string
     */
    protected string $recipient;

    /**
     * Sender number
     *
     * @param string
     */
    protected string $from;

    /**
     * Message
     *
     * @var Message|string
     */
    protected Message|string $message;

    /**
     * The response of driver webservice of sending message.
     *
     * @var string
     */
    protected string $webserviceResponse;

    /**
     * Result of sending message.
     *
     * @var bool
     */
    protected bool|null $success = null;

    public $callBackAfterSend;

    /**
     * Add recipient (phone or mobile numbers)
     *
     * @param  string  $recipient
     * @return self
     */
    public function to(string $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient mobile number.
     *
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * Get sender number (from attribute)
     *
     * @return string
     */
    public function getSenderNumber(): string
    {
        return $this->from;
    }

    /**
     * Set related message.
     *
     * @param  Message  $message
     * @return self
     */
    public function message(Message|string $message): self
    {
        $this->message = $message instanceof Message ? $message : new \HoomanMirghasemi\Sms\Message($message);

        return $this;
    }

    /**
     * Get message in string.
     *
     * @return string
     */
    public function getMessage(): string
    {
        if ($this->message instanceof Message) {
            return $this->message->toString();
        }

        return $this->message;
    }

    /**
     * Get result of sending message is successful or not.
     *
     * @return bool
     */
    public function getResult(): bool
    {
        return $this->success;
    }

    /**
     * Get webservice response.
     *
     * @return string
     */
    public function getWebServiceResponce(): string
    {
        return $this->webserviceResponse;
    }

    /**
     * Call back use for get result of sending.
     * note: It do not only work correctly when use Notification Queueable.
     *
     * @param $function
     * @return $this
     */
    public function setCallBack(callable $function): self
    {
        $this->callBackAfterSend = $function;

        return $this;
    }

    /**
     * Each driver should call this parent method at end of own send.
     * This fire SmsSentEvent
     *
     * @see SmsSentEvent
     *
     * @return bool
     */
    public function send(): bool
    {
        if ($this->success === null) {
            throw new \BadMethodCallException('Abstract driver send method should only call in end of drivers with result of send');
        }

        if ($this->callBackAfterSend != null) {
            ($this->callBackAfterSend)($this->success);
        }

        $smsSentEvent = new SmsSentEvent($this);
        Event::dispatch($smsSentEvent);

        return $this->success;
    }

    /**
     * When a driver can not connect to provider this method called.
     * Also ProviderConnectionFailedEvent fired.
     *
     * @see ProviderConnectionFailedEvent
     */
    public function failedConnectToProvider()
    {
        $reflect = new \ReflectionClass($this);
        $providerName = strtolower($reflect->getShortName());

        $this->webserviceResponse = "System can not connect to {$providerName} webservice.";
        $this->success = false;

        $providerConnectionFailedEvent = new ProviderConnectionFailedEvent($this);
        Event::dispatch($providerConnectionFailedEvent);
    }
}
