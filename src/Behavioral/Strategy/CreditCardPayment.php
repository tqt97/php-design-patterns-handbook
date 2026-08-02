<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\Strategy;

final class CreditCardPayment implements PaymentStrategy
{
    public function pay(int $amount): string
    {
        return sprintf('Paid %d by credit card', $amount);
    }
}
