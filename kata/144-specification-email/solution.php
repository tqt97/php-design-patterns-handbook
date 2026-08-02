<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface EmailSpecification
{
    public function isSatisfiedBy(array $candidate): bool;
}
final class ActiveEmailSpecification implements EmailSpecification
{
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['active'] ?? false) === true;
    }
}
final class MinimumScoreEmailSpecification implements EmailSpecification
{
    public function __construct(private int $minimum)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['score'] ?? 0) >= $this->minimum;
    }
}
final class AndEmailSpecification implements EmailSpecification
{
    public function __construct(private EmailSpecification $left, private EmailSpecification $right)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return $this->left->isSatisfiedBy($c) && $this->right->isSatisfiedBy($c);
    }
}
$spec = new AndEmailSpecification(new ActiveEmailSpecification(), new MinimumScoreEmailSpecification(70));
expect($spec->isSatisfiedBy(['active' => true, 'score' => 80]), 'eligible');
expect(!$spec->isSatisfiedBy(['active' => true, 'score' => 50]), 'not eligible');
echo 'PASS kata 144' . PHP_EOL;
