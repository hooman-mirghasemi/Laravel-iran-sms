<?php

namespace HoomanMirghasemi\Sms;

use HoomanMirghasemi\Sms\Contracts\Message as MessageContract;

class Message implements MessageContract
{
    /**
     * Plain text message.
     *
     * @param string
     */
    protected string $message;

    /**
     * Template options.
     */
    protected array $template = [
        'identifier' => null,
        'params'     => null,
    ];

    /**
     * Message constructor.
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Retrieve string format of message.
     */
    public function toString(): string
    {
        return $this->message;
    }

    /**
     * Retrieve string format of message.
     */
    public function useTemplateIfSupports(string $templateIdentifier, array $params = []): self
    {
        $this->template['identifier'] = $templateIdentifier;
        $this->template['params'] = $params;

        return $this;
    }

    /**
     * Determine if message uses a template.
     */
    public function usesTemplate(): bool
    {
        return !is_null($this->template['identifier']);
    }

    /**
     * Retrieve template options.
     */
    public function getTemplate(): array
    {
        return $this->template;
    }
}
