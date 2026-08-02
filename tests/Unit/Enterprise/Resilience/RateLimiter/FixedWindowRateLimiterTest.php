<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Resilience\RateLimiter;

use DateTimeImmutable;
use DesignPatterns\Enterprise\Resilience\RateLimiter\FixedWindowRateLimiter;
use PHPUnit\Framework\TestCase;

final class FixedWindowRateLimiterTest extends TestCase
{
    public function testItRejectsRequestsAfterTheWindowBudgetIsConsumed(): void
    {
        $limiter = new FixedWindowRateLimiter(limit: 2, windowSeconds: 60);
        $now = new DateTimeImmutable('2026-08-02T00:00:10Z');

        self::assertTrue($limiter->acquire('tenant-a', $now)->allowed);
        self::assertTrue($limiter->acquire('tenant-a', $now)->allowed);
        $rejected = $limiter->acquire('tenant-a', $now);

        self::assertFalse($rejected->allowed);
        self::assertGreaterThan(0, $rejected->retryAfterSeconds);
    }

    public function testItUsesIndependentBudgetsAndResetsAtTheNextWindow(): void
    {
        $limiter = new FixedWindowRateLimiter(limit: 1, windowSeconds: 60);
        $now = new DateTimeImmutable('2026-08-02T00:00:10Z');

        self::assertTrue($limiter->acquire('tenant-a', $now)->allowed);
        self::assertTrue($limiter->acquire('tenant-b', $now)->allowed);
        self::assertTrue($limiter->acquire('tenant-a', $now->modify('+60 seconds'))->allowed);
    }
}
