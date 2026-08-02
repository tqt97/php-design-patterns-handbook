<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ReportPolicy
{
    public function apply(int $base): int;
}
final class StandardReportPolicy implements ReportPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredReportPolicy implements ReportPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class ReportService
{
    public function __construct(private ReportPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new ReportService(new PreferredReportPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new ReportService(new StandardReportPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 109' . PHP_EOL;
