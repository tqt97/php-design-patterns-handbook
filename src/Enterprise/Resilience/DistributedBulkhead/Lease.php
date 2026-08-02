<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience\DistributedBulkhead;

use DateTimeImmutable;

final readonly class Lease
{
    public function __construct(
        public string $token,
        public string $owner,
        public DateTimeImmutable $expiresAt,
    ) {}

    public function isExpired(DateTimeImmutable $now): bool
    {
        return $now >= $this->expiresAt;
    }
}
