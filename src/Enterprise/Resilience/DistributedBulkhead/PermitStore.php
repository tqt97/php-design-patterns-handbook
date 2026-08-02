<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience\DistributedBulkhead;

use DateTimeImmutable;

interface PermitStore
{
    public function acquire(string $owner, int $capacity, DateTimeImmutable $now, DateTimeImmutable $expiresAt): ?Lease;

    public function release(string $token): bool;

    public function active(DateTimeImmutable $now): int;
}
