<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CheckoutEvent
{
    public function __construct(public string $id)
    {
    }
}
interface CheckoutListener
{
    public function handle(CheckoutEvent $event): void;
}
final class RecordingCheckoutListener implements CheckoutListener
{
    public array $seen = [];
    public function handle(CheckoutEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class CheckoutPublisher
{
    private array $listeners = [];
    public function subscribe(CheckoutListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(CheckoutEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingCheckoutListener();
$b = new RecordingCheckoutListener();
$publisher = new CheckoutPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new CheckoutEvent('checkout-101'));
expect($a->seen === ['checkout-101'] && $b->seen === ['checkout-101'], 'all listeners notified');
echo 'PASS kata 137' . PHP_EOL;
