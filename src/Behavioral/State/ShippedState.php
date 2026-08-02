<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\State;

use DomainException;

final class ShippedState implements OrderState
{
    public function pay(Order $order): void
    {
        throw new DomainException('A shipped order cannot be paid again.');
    }

    public function ship(Order $order): void
    {
        throw new DomainException('The order is already shipped.');
    }

    public function cancel(Order $order): void
    {
        throw new DomainException('A shipped order must use the return workflow.');
    }

    public function label(): string
    {
        return 'shipped';
    }
}
