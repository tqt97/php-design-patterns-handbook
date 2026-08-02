<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Resilience;

use DesignPatterns\Enterprise\Resilience\Bulkhead;
use DesignPatterns\Enterprise\Resilience\BulkheadRejectedException;
use PHPUnit\Framework\TestCase;

final class BulkheadTest extends TestCase
{
    public function test_releases_permit_after_success(): void
    {
        $bulkhead = new Bulkhead(1);

        self::assertSame('ok', $bulkhead->execute(static fn (): string => 'ok'));
        self::assertSame(0, $bulkhead->active());
        self::assertSame(1, $bulkhead->available());
    }

    public function test_releases_permit_after_failure(): void
    {
        $bulkhead = new Bulkhead(1);

        try {
            $bulkhead->execute(static fn (): never => throw new \RuntimeException('failed'));
        } catch (\RuntimeException) {
        }

        self::assertSame(0, $bulkhead->active());
    }

    public function test_rejects_nested_execution_when_capacity_is_exhausted(): void
    {
        $bulkhead = new Bulkhead(1);

        $this->expectException(BulkheadRejectedException::class);

        $bulkhead->execute(static fn (): mixed => $bulkhead->execute(static fn (): string => 'never'));
    }
}
