<?php

declare(strict_types=1);

namespace DesignPatterns\Behavioral\Strategy;

interface PaymentStrategy
{
    public function pay(int $amount): string;
}
