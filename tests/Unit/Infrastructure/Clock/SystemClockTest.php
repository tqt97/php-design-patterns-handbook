<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Clock;

use DateTimeImmutable;
use DesignPatterns\Infrastructure\Clock\SystemClock;
use PHPUnit\Framework\TestCase;

final class SystemClockTest extends TestCase
{
    public function testNowReturnsCurrentImmutableTime(): void
    {
        $before = new DateTimeImmutable('-1 second');
        $now = (new SystemClock())->now();
        $after = new DateTimeImmutable('+1 second');

        self::assertGreaterThanOrEqual($before, $now);
        self::assertLessThanOrEqual($after, $now);
    }
}
