<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface AuditSpecification
{
    public function isSatisfiedBy(array $candidate): bool;
}
final class ActiveAuditSpecification implements AuditSpecification
{
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['active'] ?? false) === true;
    }
}
final class MinimumScoreAuditSpecification implements AuditSpecification
{
    public function __construct(private int $minimum)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['score'] ?? 0) >= $this->minimum;
    }
}
final class AndAuditSpecification implements AuditSpecification
{
    public function __construct(private AuditSpecification $left, private AuditSpecification $right)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return $this->left->isSatisfiedBy($c) && $this->right->isSatisfiedBy($c);
    }
}
$spec = new AndAuditSpecification(new ActiveAuditSpecification(), new MinimumScoreAuditSpecification(70));
expect($spec->isSatisfiedBy(['active' => true, 'score' => 80]), 'eligible');
expect(!$spec->isSatisfiedBy(['active' => true, 'score' => 50]), 'not eligible');
echo 'PASS kata 108' . PHP_EOL;
