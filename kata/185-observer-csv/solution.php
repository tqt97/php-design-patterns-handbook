<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CsvEvent
{
    public function __construct(public string $id)
    {
    }
}
interface CsvListener
{
    public function handle(CsvEvent $event): void;
}
final class RecordingCsvListener implements CsvListener
{
    public array $seen = [];
    public function handle(CsvEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class CsvPublisher
{
    private array $listeners = [];
    public function subscribe(CsvListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(CsvEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingCsvListener();
$b = new RecordingCsvListener();
$publisher = new CsvPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new CsvEvent('customers.csv'));
expect($a->seen === ['customers.csv'] && $b->seen === ['customers.csv'], 'all listeners notified');
echo 'PASS kata 185' . PHP_EOL;
