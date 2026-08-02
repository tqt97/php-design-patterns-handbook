<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InventorySpecification
{
    public function isSatisfiedBy(array $candidate): bool;
}
final class ActiveInventorySpecification implements InventorySpecification
{
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['active'] ?? false) === true;
    }
}
final class MinimumScoreInventorySpecification implements InventorySpecification
{
    public function __construct(private int $minimum)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return ($c['score'] ?? 0) >= $this->minimum;
    }
}
final class AndInventorySpecification implements InventorySpecification
{
    public function __construct(private InventorySpecification $left, private InventorySpecification $right)
    {
    }
    public function isSatisfiedBy(array $c): bool
    {
        return $this->left->isSatisfiedBy($c) && $this->right->isSatisfiedBy($c);
    }
}
$spec = new AndInventorySpecification(new ActiveInventorySpecification(), new MinimumScoreInventorySpecification(70));
expect($spec->isSatisfiedBy(['active' => true, 'score' => 80]), 'eligible');
expect(!$spec->isSatisfiedBy(['active' => true, 'score' => 50]), 'not eligible');
echo 'PASS kata 192' . PHP_EOL;
