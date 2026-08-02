<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\State;

use DomainException;

final class PendingState implements OrderState
{
    public function pay(Order $order): void
    {
        $order->transitionTo(new PaidState());
    }

    public function ship(Order $order): void
    {
        throw new DomainException('A pending order cannot be shipped.');
    }

    public function cancel(Order $order): void
    {
        $order->transitionTo(new CancelledState());
    }

    public function label(): string
    {
        return 'pending';
    }
}
