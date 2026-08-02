<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\Observer;

final class EventDispatcher
{
    /** @var array<string, list<EventListener>> */
    private array $listeners = [];

    public function subscribe(string $eventName, EventListener $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch(Event $event): void
    {
        foreach ($this->listeners[$event->name()] ?? [] as $listener) {
            $listener->handle($event);
        }
    }
}
