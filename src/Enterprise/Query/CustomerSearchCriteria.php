<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Query;

final readonly class CustomerSearchCriteria
{
    public function __construct(
        public ?string $emailContains = null,
        public ?bool $active = null,
        public int $limit = 50,
    ) {
        if ($limit < 1 || $limit > 500) {
            throw new \InvalidArgumentException('Limit must be between 1 and 500.');
        }
    }
}
