<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class InventoryEvent
{
    public function __construct(public string $id)
    {
    }
}
interface InventoryListener
{
    public function handle(InventoryEvent $event): void;
}
final class RecordingInventoryListener implements InventoryListener
{
    public array $seen = [];
    public function handle(InventoryEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class InventoryPublisher
{
    private array $listeners = [];
    public function subscribe(InventoryListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(InventoryEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingInventoryListener();
$b = new RecordingInventoryListener();
$publisher = new InventoryPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new InventoryEvent('SKU-PHP-01'));
expect($a->seen === ['SKU-PHP-01'] && $b->seen === ['SKU-PHP-01'], 'all listeners notified');
echo 'PASS kata 5' . PHP_EOL;
