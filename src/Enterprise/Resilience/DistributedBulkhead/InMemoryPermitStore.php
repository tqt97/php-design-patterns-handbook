<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience\DistributedBulkhead;

use DateTimeImmutable;

final class InMemoryPermitStore implements PermitStore
{
    /** @var array<string, Lease> */
    private array $leases = [];

    public function acquire(string $owner, int $capacity, DateTimeImmutable $now, DateTimeImmutable $expiresAt): ?Lease
    {
        $this->purgeExpired($now);
        if (count($this->leases) >= $capacity) {
            return null;
        }

        $token = hash('sha256', $owner . '|' . $expiresAt->format(DATE_ATOM) . '|' . count($this->leases));
        $lease = new Lease($token, $owner, $expiresAt);
        $this->leases[$token] = $lease;

        return $lease;
    }

    public function release(string $token): bool
    {
        if (! isset($this->leases[$token])) {
            return false;
        }
        unset($this->leases[$token]);

        return true;
    }

    public function active(DateTimeImmutable $now): int
    {
        $this->purgeExpired($now);
        return count($this->leases);
    }

    private function purgeExpired(DateTimeImmutable $now): void
    {
        foreach ($this->leases as $token => $lease) {
            if ($lease->isExpired($now)) {
                unset($this->leases[$token]);
            }
        }
    }
}
