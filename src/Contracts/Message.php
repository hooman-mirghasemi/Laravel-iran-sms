<?php

namespace HoomanMirghasemi\Sms\Contracts;

interface Message
{
    /**
     * Retrieve string format of message.
     */
    public function toString(): string;
}
