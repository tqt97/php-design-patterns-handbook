<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface OrderPolicy
{
    public function apply(int $base): int;
}
final class StandardOrderPolicy implements OrderPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredOrderPolicy implements OrderPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class OrderService
{
    public function __construct(private OrderPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new OrderService(new PreferredOrderPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new OrderService(new StandardOrderPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 157' . PHP_EOL;
