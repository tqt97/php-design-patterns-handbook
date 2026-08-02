<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface UserPolicy
{
    public function apply(int $base): int;
}
final class StandardUserPolicy implements UserPolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredUserPolicy implements UserPolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class UserService
{
    public function __construct(private UserPolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new UserService(new PreferredUserPolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new UserService(new StandardUserPolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 133' . PHP_EOL;
