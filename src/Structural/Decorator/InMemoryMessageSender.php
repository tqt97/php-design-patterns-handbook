<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator;

final class InMemoryMessageSender implements MessageSender
{
    /** @var list<array{recipient: string, message: string}> */
    private array $sent = [];

    public function send(string $recipient, string $message): void
    {
        $this->sent[] = ['recipient' => $recipient, 'message' => $message];
    }

    /** @return list<array{recipient: string, message: string}> */
    public function sent(): array
    {
        return $this->sent;
    }
}
