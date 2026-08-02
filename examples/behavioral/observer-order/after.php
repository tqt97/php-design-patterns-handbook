<?php

declare(strict_types=1);

interface OrderCompletedListener
{
    public function handle(int $orderId): void;
}
final class SendEmail implements OrderCompletedListener
{
    public function handle(int $orderId): void
    {
        echo "Email for {$orderId}\n";
    }
}
final class UpdateAnalytics implements OrderCompletedListener
{
    public function handle(int $orderId): void
    {
        echo "Analytics for {$orderId}\n";
    }
}
final class OrderService
{
    public function __construct(private array $listeners)
    {
    }
    public function complete(int $orderId): void
    {
        echo "Save order {$orderId}\n";
        foreach ($this->listeners as $listener) {
            $listener->handle($orderId);
        }
    }
}
(new OrderService([new SendEmail(), new UpdateAnalytics()]))->complete(1001);
