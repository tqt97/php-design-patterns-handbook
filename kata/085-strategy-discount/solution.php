<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface DiscountPolicy
{
    public function apply(int $base): int;
}
final class StandardDiscountPolicy implements DiscountPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredDiscountPolicy implements DiscountPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class DiscountService
{
    public function __construct(private DiscountPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new DiscountService(new PreferredDiscountPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new DiscountService(new StandardDiscountPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 85' . PHP_EOL;
