<?php

declare(strict_types=1);
require dirname(__DIR__) . '/Benchmark.php';

final class OrderPlaced
{
    public function __construct(public int $orderId)
    {
    }
}
final class AuditListener
{
    public function __invoke(OrderPlaced $event): int
    {
        return $event->orderId + 1;
    }
}
final class MetricListener
{
    public function __invoke(OrderPlaced $event): int
    {
        return $event->orderId + 2;
    }
}
final class Dispatcher
{
    /** @param list<callable(OrderPlaced): int> $listeners */
    public function __construct(private array $listeners)
    {
    }
    public function dispatch(OrderPlaced $event): int
    {
        $sum = 0;
        foreach ($this->listeners as $listener) {
            $sum += $listener($event);
        }
        return $sum;
    }
}
$audit = new AuditListener();
$metric = new MetricListener();
$dispatcher = new Dispatcher([$audit, $metric]);
$event = new OrderPlaced(100);
$results = [
    'direct calls' => Benchmark::measure(fn(): int => $audit($event) + $metric($event)),
    'sync dispatcher' => Benchmark::measure(fn(): int => $dispatcher->dispatch($event)),
];
Benchmark::report('Event Sync vs Direct Call', $results);
