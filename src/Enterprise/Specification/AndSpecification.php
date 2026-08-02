<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Specification;

final readonly class AndSpecification implements Specification
{
    /** @param non-empty-list<Specification> $specifications */
    public function __construct(private array $specifications)
    {
        if ($specifications === []) {
            throw new \InvalidArgumentException('At least one specification is required.');
        }
    }

    public function isSatisfiedBy(object $candidate): bool
    {
        foreach ($this->specifications as $specification) {
            if (! $specification->isSatisfiedBy($candidate)) {
                return false;
            }
        }

        return true;
    }
}
