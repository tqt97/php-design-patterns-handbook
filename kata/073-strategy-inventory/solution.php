<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InventoryPolicy
{
    public function apply(int $base): int;
}
final class StandardInventoryPolicy implements InventoryPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredInventoryPolicy implements InventoryPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class InventoryService
{
    public function __construct(private InventoryPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new InventoryService(new PreferredInventoryPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new InventoryService(new StandardInventoryPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 73' . PHP_EOL;
