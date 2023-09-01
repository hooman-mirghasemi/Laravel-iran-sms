<?php

namespace HoomanMirghasemi\Sms\Contracts;

interface Driver
{
    /**
     * Add recipient (mobile numbers).
     *
     * @param string $recipient
     *
     * @return self
     */
    public function to(string $recipient): self;

    /**
     * Set related message.
     *
     * @param Message $message
     *
     * @return self
     */
    public function message(Message $message): self;

    /**
     * Send message to recipient.
     *
     * @return bool
     */
    public function send(): bool;

    /**
     * Get remaining balance of driver.
     *
     * @return string
     */
    public function getBalance(): string;
}
