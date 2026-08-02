<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\Observer;

interface EventListener
{
    public function handle(Event $event): void;
}
