<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CrmEvent
{
    public function __construct(public string $id)
    {
    }
}
interface CrmListener
{
    public function handle(CrmEvent $event): void;
}
final class RecordingCrmListener implements CrmListener
{
    public array $seen = [];
    public function handle(CrmEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class CrmPublisher
{
    private array $listeners = [];
    public function subscribe(CrmListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(CrmEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingCrmListener();
$b = new RecordingCrmListener();
$publisher = new CrmPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new CrmEvent('lead-202'));
expect($a->seen === ['lead-202'] && $b->seen === ['lead-202'], 'all listeners notified');
echo 'PASS kata 53' . PHP_EOL;
