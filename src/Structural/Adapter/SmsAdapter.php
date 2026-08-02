<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

final readonly class SmsAdapter implements MessageSender
{
    public function __construct(private LegacySmsClient $client)
    {
    }

    public function send(string $recipient, string $message): void
    {
        $this->client->sendSms($recipient, $message);
    }
}
