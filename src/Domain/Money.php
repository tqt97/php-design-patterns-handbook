<?php

declare(strict_types=1);

namespace DesignPatterns\Domain;

use DomainException;
use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        public int $minor,
        public string $currency,
    ) {
        if ($minor < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative.');
        }

        if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
            throw new InvalidArgumentException('Currency must be an ISO 4217 uppercase code.');
        }
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->minor + $other->minor, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        if ($other->minor > $this->minor) {
            throw new DomainException('Money subtraction cannot produce a negative amount.');
        }

        return new self($this->minor - $other->minor, $this->currency);
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Currency mismatch.');
        }
    }
}
