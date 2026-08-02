<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use DesignPatterns\Domain\Money;
use DomainException;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testItAddsAndSubtractsAmountsInTheSameCurrency(): void
    {
        $total = (new Money(1_000, 'USD'))->add(new Money(250, 'USD'));

        self::assertSame(1_250, $total->minor);
        self::assertSame(1_000, $total->subtract(new Money(250, 'USD'))->minor);
    }

    public function testItRejectsCurrencyMismatch(): void
    {
        $this->expectException(DomainException::class);

        (new Money(1_000, 'USD'))->add(new Money(100, 'JPY'));
    }
}
