<?php

declare(strict_types=1);

namespace Tests\Unit\Behavioral\Observer;

use DesignPatterns\Behavioral\Observer\Event;
use DesignPatterns\Behavioral\Observer\EventDispatcher;
use DesignPatterns\Behavioral\Observer\EventListener;
use PHPUnit\Framework\TestCase;

final class EventDispatcherTest extends TestCase
{
    public function test_dispatches_only_to_matching_subscribers(): void
    {
        $received = [];
        $listener = new class($received) implements EventListener {
            /** @param array<int,string> $received */
            public function __construct(private array &$received) {}
            public function handle(Event $event): void { $this->received[] = $event->name(); }
        };
        $event = new class implements Event { public function name(): string { return 'order.paid'; } };
        $dispatcher = new EventDispatcher();
        $dispatcher->subscribe('order.paid', $listener);
        $dispatcher->dispatch($event);
        self::assertSame(['order.paid'], $received);
    }
}
