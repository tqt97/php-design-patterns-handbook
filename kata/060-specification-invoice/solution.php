<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InvoiceSpecification
{
    public function isSatisfiedBy(array $candidate): bool;
}
final class ActiveInvoiceSpecification implements InvoiceSpecification
{
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['active'] ?? false) === true;
    }
}
final class MinimumScoreInvoiceSpecification implements InvoiceSpecification
{
    public function __construct(private int $minimum)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['score'] ?? 0) >= $this->minimum;
    }
}
final class AndInvoiceSpecification implements InvoiceSpecification
{
    public function __construct(private InvoiceSpecification $left, private InvoiceSpecification $right)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return $this->left->isSatisfiedBy($c) && $this->right->isSatisfiedBy($c);
    }
}
$spec = new AndInvoiceSpecification(new ActiveInvoiceSpecification(), new MinimumScoreInvoiceSpecification(70));
expect($spec->isSatisfiedBy(['active' => true, 'score' => 80]), 'eligible');
expect(!$spec->isSatisfiedBy(['active' => true, 'score' => 50]), 'not eligible');
echo 'PASS kata 60' . PHP_EOL;
