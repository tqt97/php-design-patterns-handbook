<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface EmailPolicy
{
    public function apply(int $base): int;
}
final class StandardEmailPolicy implements EmailPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredEmailPolicy implements EmailPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class EmailService
{
    public function __construct(private EmailPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new EmailService(new PreferredEmailPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new EmailService(new StandardEmailPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 25' . PHP_EOL;
