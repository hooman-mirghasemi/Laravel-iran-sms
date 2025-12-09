<?php

namespace HoomanMirghasemi\Sms\Abstracts;

use HoomanMirghasemi\Sms\Contracts\Driver as DriverContract;
use HoomanMirghasemi\Sms\Contracts\Message;
use HoomanMirghasemi\Sms\Events\ProviderConnectionFailedEvent;
use HoomanMirghasemi\Sms\Events\SmsSentEvent;
use Illuminate\Support\Facades\Event;

abstract class Driver implements DriverContract
{
    /**
     * If it can not connect to provider this get false value.
     */
    protected bool $serviceActive = true;

    /**
     * Recipient (mobile number in E164 format).
     *
     * @param string
     */
    protected string $recipient;

    /**
     * Sender number.
     *
     * @param string
     */
    protected string $from;

    /**
     * Message.
     */
    protected Message|string $message;

    /**
     * The response of driver webservice of sending message.
     */
    protected string $webserviceResponse;

    /**
     * Result of sending message.
     */
    protected ?bool $success = null;

    /**
     * Add recipient (phone or mobile numbers).
     */
    public function to(string $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient mobile number.
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * Get sender number (from attribute).
     */
    public function getSenderNumber(): string
    {
        return $this->from;
    }

    /**
     * Set related message.
     *
     * @param Message $message
     */
    public function message(Message|string $message): self
    {
        $this->message = $message instanceof Message ? $message : new \HoomanMirghasemi\Sms\Message($message);

        return $this;
    }

    /**
     * Get message in string.
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
     */
    public function getResult(): bool
    {
        return $this->success;
    }

    /**
     * Get webservice response.
     */
    public function getWebServiceResponce(): string
    {
        return $this->webserviceResponse;
    }

    /**
     * Each driver should call this parent method at end of own send.
     * This fire SmsSentEvent.
     *
     * @see SmsSentEvent
     */
    public function send(): bool
    {
        if (null === $this->success) {
            throw new \BadMethodCallException('Abstract driver send method should only call in end of drivers with result of send');
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
