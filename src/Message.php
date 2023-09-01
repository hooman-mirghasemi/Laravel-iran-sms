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
     *
     * @var array
     */
    protected array $template = [
        'identifier' => null,
        'params'     => null,
    ];

    /**
     * Message constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Retrieve string format of message.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->message;
    }

    /**
     * Retrieve string format of message.
     *
     * @param string $templateIdentifier
     * @param array  $params
     *
     * @return self
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
     *
     * @return array
     */
    public function getTemplate(): array
    {
        return $this->template;
    }
}
