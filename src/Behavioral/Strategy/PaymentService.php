<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\Strategy;

use InvalidArgumentException;

final readonly class PaymentService
{
    public function __construct(private PaymentStrategy $strategy)
    {
    }

    public function pay(int $amount): string
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than zero.');
        }

        return $this->strategy->pay($amount);
    }
}
