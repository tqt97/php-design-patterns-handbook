<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\State;

use DomainException;

final class PaidState implements OrderState
{
    public function pay(Order $order): void
    {
        throw new DomainException('The order is already paid.');
    }

    public function ship(Order $order): void
    {
        $order->transitionTo(new ShippedState());
    }

    public function cancel(Order $order): void
    {
        throw new DomainException('Refund is required before cancelling a paid order.');
    }

    public function label(): string
    {
        return 'paid';
    }
}
