<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class AuditEvent
{
    public function __construct(public string $id)
    {
    }
}
interface AuditListener
{
    public function handle(AuditEvent $event): void;
}
final class RecordingAuditListener implements AuditListener
{
    public array $seen = [];
    public function handle(AuditEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class AuditPublisher
{
    private array $listeners = [];
    public function subscribe(AuditListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(AuditEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingAuditListener();
$b = new RecordingAuditListener();
$publisher = new AuditPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new AuditEvent('user.updated'));
expect($a->seen === ['user.updated'] && $b->seen === ['user.updated'], 'all listeners notified');
echo 'PASS kata 125' . PHP_EOL;
