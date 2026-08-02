<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator;

interface MessageSender
{
    public function send(string $recipient, string $message): void;
}
