<?php

declare(strict_types=1);

namespace Tests\Unit\Behavioral\Strategy;

use DesignPatterns\Behavioral\Strategy\CreditCardPayment;
use DesignPatterns\Behavioral\Strategy\PaymentService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PaymentServiceTest extends TestCase
{
    public function test_it_delegates_payment_to_strategy(): void
    {
        self::assertSame('Paid 1000 by credit card', (new PaymentService(new CreditCardPayment()))->pay(1000));
    }

    public function test_it_rejects_invalid_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new PaymentService(new CreditCardPayment()))->pay(0);
    }
}
