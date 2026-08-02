<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SearchPolicy
{
    public function apply(int $base): int;
}
final class StandardSearchPolicy implements SearchPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredSearchPolicy implements SearchPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class SearchService
{
    public function __construct(private SearchPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new SearchService(new PreferredSearchPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new SearchService(new StandardSearchPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 37' . PHP_EOL;
