<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class UserEvent
{
    public function __construct(public string $id)
    {
    }
}
interface UserListener
{
    public function handle(UserEvent $event): void;
}
final class RecordingUserListener implements UserListener
{
    public array $seen = [];
    public function handle(UserEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class UserPublisher
{
    private array $listeners = [];
    public function subscribe(UserListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(UserEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingUserListener();
$b = new RecordingUserListener();
$publisher = new UserPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new UserEvent('user-42'));
expect($a->seen === ['user-42'] && $b->seen === ['user-42'], 'all listeners notified');
echo 'PASS kata 65' . PHP_EOL;
