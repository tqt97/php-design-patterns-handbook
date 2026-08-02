<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class OrderEvent
{
    public function __construct(public string $id)
    {
    }
}
interface OrderListener
{
    public function handle(OrderEvent $event): void;
}
final class RecordingOrderListener implements OrderListener
{
    public array $seen = [];
    public function handle(OrderEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class OrderPublisher
{
    private array $listeners = [];
    public function subscribe(OrderListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(OrderEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingOrderListener();
$b = new RecordingOrderListener();
$publisher = new OrderPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new OrderEvent('ORD-1001'));
expect($a->seen === ['ORD-1001'] && $b->seen === ['ORD-1001'], 'all listeners notified');
echo 'PASS kata 89' . PHP_EOL;
