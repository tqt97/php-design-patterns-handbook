<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class PaymentEvent
{
    public function __construct(public string $id)
    {
    }
}
interface PaymentListener
{
    public function handle(PaymentEvent $event): void;
}
final class RecordingPaymentListener implements PaymentListener
{
    public array $seen = [];
    public function handle(PaymentEvent $event): void
    {
        $this->seen[] = $event->id;
    }
}
final class PaymentPublisher
{
    private array $listeners = [];
    public function subscribe(PaymentListener $l): void
    {
        $this->listeners[] = $l;
    }
    public function publish(PaymentEvent $e): void
    {
        foreach ($this->listeners as $l)
            $l->handle($e);
    }
}
$a = new RecordingPaymentListener();
$b = new RecordingPaymentListener();
$publisher = new PaymentPublisher();
$publisher->subscribe($a);
$publisher->subscribe($b);
$publisher->publish(new PaymentEvent('pay_1001'));
expect($a->seen === ['pay_1001'] && $b->seen === ['pay_1001'], 'all listeners notified');
echo 'PASS kata 113' . PHP_EOL;
