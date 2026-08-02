<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class DiscountEvent
{
    public function __construct(public string $id)
    {
    }
}
interface DiscountListener
{
    public function handle(DiscountEvent $event): void;
}
final class RecordingDiscountListener implements DiscountListener
{
    public array $seen = [];
    public function handle(DiscountEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class DiscountPublisher
{
    private array $listeners = [];
    public function subscribe(DiscountListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(DiscountEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingDiscountListener();
$b = new RecordingDiscountListener();
$publisher = new DiscountPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new DiscountEvent('VIP20'));
expect($a->seen === ['VIP20'] && $b->seen === ['VIP20'], 'all listeners notified');
echo 'PASS kata 17' . PHP_EOL;
