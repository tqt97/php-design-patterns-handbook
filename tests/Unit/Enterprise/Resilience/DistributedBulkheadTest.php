<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Resilience;

use DateTimeImmutable;
use DesignPatterns\Enterprise\Resilience\BulkheadRejectedException;
use DesignPatterns\Enterprise\Resilience\DistributedBulkhead\DistributedBulkhead;
use DesignPatterns\Enterprise\Resilience\DistributedBulkhead\InMemoryPermitStore;
use PHPUnit\Framework\TestCase;

final class DistributedBulkheadTest extends TestCase
{
    public function testRejectsWhenCapacityIsExhaustedAndRecoversAfterRelease(): void
    {
        $now = new DateTimeImmutable('2026-08-02T00:00:00Z');
        $bulkhead = new DistributedBulkhead(new InMemoryPermitStore(), 1, 30);
        $lease = $bulkhead->acquire('worker-a', $now);

        $this->expectException(BulkheadRejectedException::class);
        try {
            $bulkhead->acquire('worker-b', $now);
        } finally {
            self::assertTrue($bulkhead->release($lease));
        }
    }
}
