<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CacheEvent
{
    public function __construct(public string $id)
    {
    }
}
interface CacheListener
{
    public function handle(CacheEvent $event): void;
}
final class RecordingCacheListener implements CacheListener
{
    public array $seen = [];
    public function handle(CacheEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class CachePublisher
{
    private array $listeners = [];
    public function subscribe(CacheListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(CacheEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingCacheListener();
$b = new RecordingCacheListener();
$publisher = new CachePublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new CacheEvent('customer:42'));
expect($a->seen === ['customer:42'] && $b->seen === ['customer:42'], 'all listeners notified');
echo 'PASS kata 149' . PHP_EOL;
