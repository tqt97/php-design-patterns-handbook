<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Migration;

final readonly class ComparisonResult
{
    /** @param array<string, mixed> $differences */
    public function __construct(
        public mixed $authoritative,
        public mixed $shadow,
        public array $differences,
    ) {}

    public function equivalent(): bool
    {
        return $this->differences === [];
    }
}
