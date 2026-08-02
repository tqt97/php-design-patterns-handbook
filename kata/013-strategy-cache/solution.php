<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CachePolicy
{
    public function apply(int $base): int;
}
final class StandardCachePolicy implements CachePolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredCachePolicy implements CachePolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class CacheService
{
    public function __construct(private CachePolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new CacheService(new PreferredCachePolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new CacheService(new StandardCachePolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 13' . PHP_EOL;
