<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class ReportEvent
{
    public function __construct(public string $id)
    {
    }
}
interface ReportListener
{
    public function handle(ReportEvent $event): void;
}
final class RecordingReportListener implements ReportListener
{
    public array $seen = [];
    public function handle(ReportEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class ReportPublisher
{
    private array $listeners = [];
    public function subscribe(ReportListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(ReportEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingReportListener();
$b = new RecordingReportListener();
$publisher = new ReportPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new ReportEvent('sales-monthly'));
expect($a->seen === ['sales-monthly'] && $b->seen === ['sales-monthly'], 'all listeners notified');
echo 'PASS kata 41' . PHP_EOL;
