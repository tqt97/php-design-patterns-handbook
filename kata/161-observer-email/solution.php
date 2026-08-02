<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class EmailEvent
{
    public function __construct(public string $id)
    {
    }
}
interface EmailListener
{
    public function handle(EmailEvent $event): void;
}
final class RecordingEmailListener implements EmailListener
{
    public array $seen = [];
    public function handle(EmailEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class EmailPublisher
{
    private array $listeners = [];
    public function subscribe(EmailListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(EmailEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingEmailListener();
$b = new RecordingEmailListener();
$publisher = new EmailPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new EmailEvent('welcome@example.com'));
expect($a->seen === ['welcome@example.com'] && $b->seen === ['welcome@example.com'], 'all listeners notified');
echo 'PASS kata 161' . PHP_EOL;
