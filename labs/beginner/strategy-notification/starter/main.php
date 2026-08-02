<?php

declare(strict_types=1);

final class NotificationService
{
    public function send(string $channel, string $recipient, string $message): string
    {
        if ($channel === 'email') {
            return "Email to {$recipient}: {$message}";
        }
        if ($channel === 'sms') {
            return "SMS to {$recipient}: {$message}";
        }
        throw new InvalidArgumentException('Unsupported channel');
    }
}
