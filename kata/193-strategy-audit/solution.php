<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface AuditPolicy
{
    public function apply(int $base): int;
}
final class StandardAuditPolicy implements AuditPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredAuditPolicy implements AuditPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class AuditService
{
    public function __construct(private AuditPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new AuditService(new PreferredAuditPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new AuditService(new StandardAuditPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 193' . PHP_EOL;
