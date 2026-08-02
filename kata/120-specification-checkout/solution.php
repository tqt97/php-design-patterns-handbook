<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CheckoutSpecification
{
    public function isSatisfiedBy(array $candidate): bool;
}
final class ActiveCheckoutSpecification implements CheckoutSpecification
{
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['active'] ?? false) === true;
    }
}
final class MinimumScoreCheckoutSpecification implements CheckoutSpecification
{
    public function __construct(private int $minimum)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['score'] ?? 0) >= $this->minimum;
    }
}
final class AndCheckoutSpecification implements CheckoutSpecification
{
    public function __construct(private CheckoutSpecification $left, private CheckoutSpecification $right)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return $this->left->isSatisfiedBy($c) && $this->right->isSatisfiedBy($c);
    }
}
$spec = new AndCheckoutSpecification(new ActiveCheckoutSpecification(), new MinimumScoreCheckoutSpecification(70));
expect($spec->isSatisfiedBy(['active' => true, 'score' => 80]), 'eligible');
expect(!$spec->isSatisfiedBy(['active' => true, 'score' => 50]), 'not eligible');
echo 'PASS kata 120' . PHP_EOL;
