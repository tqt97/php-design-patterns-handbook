<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InvoicePolicy
{
    public function apply(int $base): int;
}
final class StandardInvoicePolicy implements InvoicePolicy
{
    public function apply(int $base): int
    {
        return $base;
    }
}
final class PreferredInvoicePolicy implements InvoicePolicy
{
    public function apply(int $base): int
    {
        return max(0, $base - 2000);
    }
}
final class InvoiceService
{
    public function __construct(private InvoicePolicy $policy)
    {
    }
    public function calculate(int $base): int
    {
        if ($base < 0)
            throw new InvalidArgumentException('base');
        return $this->policy->apply($base);
    }
}
expect((new InvoiceService(new PreferredInvoicePolicy()))->calculate(10000) === 8000, 'preferred policy');
expect((new InvoiceService(new StandardInvoicePolicy()))->calculate(10000) === 10000, 'standard policy');
echo 'PASS kata 145' . PHP_EOL;
