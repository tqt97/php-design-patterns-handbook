<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class SupportEvent
{
    public function __construct(public string $id)
    {
    }
}
interface SupportListener
{
    public function handle(SupportEvent $event): void;
}
final class RecordingSupportListener implements SupportListener
{
    public array $seen = [];
    public function handle(SupportEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class SupportPublisher
{
    private array $listeners = [];
    public function subscribe(SupportListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(SupportEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingSupportListener();
$b = new RecordingSupportListener();
$publisher = new SupportPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new SupportEvent('TICKET-88'));
expect($a->seen === ['TICKET-88'] && $b->seen === ['TICKET-88'], 'all listeners notified');
echo 'PASS kata 101' . PHP_EOL;
