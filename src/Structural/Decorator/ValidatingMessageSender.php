<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator;

use InvalidArgumentException;

final readonly class ValidatingMessageSender implements MessageSender
{
    public function __construct(private MessageSender $inner)
    {
    }

    public function send(string $recipient, string $message): void
    {
        if (trim($recipient) === '' || trim($message) === '') {
            throw new InvalidArgumentException('Recipient and message are required.');
        }

        $this->inner->send($recipient, $message);
    }
}
