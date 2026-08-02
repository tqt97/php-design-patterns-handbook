<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\State;

use DomainException;

final class CancelledState implements OrderState
{
    public function pay(Order $order): void
    {
        throw new DomainException('A cancelled order cannot be paid.');
    }

    public function ship(Order $order): void
    {
        throw new DomainException('A cancelled order cannot be shipped.');
    }

    public function cancel(Order $order): void
    {
        throw new DomainException('The order is already cancelled.');
    }

    public function label(): string
    {
        return 'cancelled';
    }
}
