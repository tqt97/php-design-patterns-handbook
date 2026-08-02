<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ShippingSpecification
{
    public function isSatisfiedBy(array $candidate): bool;
}
final class ActiveShippingSpecification implements ShippingSpecification
{
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['active'] ?? false) === true;
    }
}
final class MinimumScoreShippingSpecification implements ShippingSpecification
{
    public function __construct(private int $minimum)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['score'] ?? 0) >= $this->minimum;
    }
}
final class AndShippingSpecification implements ShippingSpecification
{
    public function __construct(private ShippingSpecification $left, private ShippingSpecification $right)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return $this->left->isSatisfiedBy($c) && $this->right->isSatisfiedBy($c);
    }
}
$spec = new AndShippingSpecification(new ActiveShippingSpecification(), new MinimumScoreShippingSpecification(70));
expect($spec->isSatisfiedBy(['active' => true, 'score' => 80]), 'eligible');
expect(!$spec->isSatisfiedBy(['active' => true, 'score' => 50]), 'not eligible');
echo 'PASS kata 180' . PHP_EOL;
