<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class InvoiceEvent
{
    public function __construct(public string $id)
    {
    }
}
interface InvoiceListener
{
    public function handle(InvoiceEvent $event): void;
}
final class RecordingInvoiceListener implements InvoiceListener
{
    public array $seen = [];
    public function handle(InvoiceEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class InvoicePublisher
{
    private array $listeners = [];
    public function subscribe(InvoiceListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(InvoiceEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingInvoiceListener();
$b = new RecordingInvoiceListener();
$publisher = new InvoicePublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new InvoiceEvent('INV-2026-001'));
expect($a->seen === ['INV-2026-001'] && $b->seen === ['INV-2026-001'], 'all listeners notified');
echo 'PASS kata 77' . PHP_EOL;
