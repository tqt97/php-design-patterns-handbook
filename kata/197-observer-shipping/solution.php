<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class ShippingEvent
{
    public function __construct(public string $id)
    {
    }
}
interface ShippingListener
{
    public function handle(ShippingEvent $event): void;
}
final class RecordingShippingListener implements ShippingListener
{
    public array $seen = [];
    public function handle(ShippingEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class ShippingPublisher
{
    private array $listeners = [];
    public function subscribe(ShippingListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(ShippingEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingShippingListener();
$b = new RecordingShippingListener();
$publisher = new ShippingPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new ShippingEvent('HCM-HN'));
expect($a->seen === ['HCM-HN'] && $b->seen === ['HCM-HN'], 'all listeners notified');
echo 'PASS kata 197' . PHP_EOL;
