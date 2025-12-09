<?php

namespace HoomanMirghasemi\Sms\Contracts;

interface Driver
{
    /**
     * Add recipient (mobile numbers).
     */
    public function to(string $recipient): self;

    /**
     * Set related message.
     */
    public function message(Message $message): self;

    /**
     * Send message to recipient.
     */
    public function send(): bool;

    /**
     * Get remaining balance of driver.
     */
    public function getBalance(): string;
}
