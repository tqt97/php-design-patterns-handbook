<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ShippingPolicy
{
    public function apply(int $base): int;
}
final class StandardShippingPolicy implements ShippingPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredShippingPolicy implements ShippingPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class ShippingService
{
    public function __construct(private ShippingPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new ShippingService(new PreferredShippingPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new ShippingService(new StandardShippingPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 61' . PHP_EOL;
