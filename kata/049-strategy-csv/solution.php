<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CsvPolicy
{
    public function apply(int $base): int;
}
final class StandardCsvPolicy implements CsvPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredCsvPolicy implements CsvPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class CsvService
{
    public function __construct(private CsvPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new CsvService(new PreferredCsvPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new CsvService(new StandardCsvPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 49' . PHP_EOL;
