<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience\DistributedBulkhead;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;
use DesignPatterns\Enterprise\Resilience\BulkheadRejectedException;

final readonly class DistributedBulkhead
{
    public function __construct(
        private PermitStore $store,
        private int $capacity,
        private int $leaseSeconds,
    ) {
        if ($capacity < 1 || $leaseSeconds < 1) {
            throw new InvalidArgumentException('Capacity and lease duration must be positive.');
        }
    }

    public function acquire(string $owner, DateTimeImmutable $now): Lease
    {
        $expiresAt = $now->add(new DateInterval('PT' . $this->leaseSeconds . 'S'));
        $lease = $this->store->acquire($owner, $this->capacity, $now, $expiresAt);
        if ($lease === null) {
            throw new BulkheadRejectedException('Distributed bulkhead capacity is exhausted.');
        }

        return $lease;
    }

    public function release(Lease $lease): bool
    {
        return $this->store->release($lease->token);
    }
}
