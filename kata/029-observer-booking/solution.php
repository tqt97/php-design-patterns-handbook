<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class BookingEvent
{
    public function __construct(public string $id)
    {
    }
}
interface BookingListener
{
    public function handle(BookingEvent $event): void;
}
final class RecordingBookingListener implements BookingListener
{
    public array $seen = [];
    public function handle(BookingEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class BookingPublisher
{
    private array $listeners = [];
    public function subscribe(BookingListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(BookingEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingBookingListener();
$b = new RecordingBookingListener();
$publisher = new BookingPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new BookingEvent('room-12'));
expect($a->seen === ['room-12'] && $b->seen === ['room-12'], 'all listeners notified');
echo 'PASS kata 29' . PHP_EOL;
