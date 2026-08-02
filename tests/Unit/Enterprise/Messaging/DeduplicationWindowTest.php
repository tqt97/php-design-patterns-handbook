<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Messaging;

use DateTimeImmutable;
use DesignPatterns\Enterprise\Messaging\DeduplicationWindow;
use PHPUnit\Framework\TestCase;

final class DeduplicationWindowTest extends TestCase
{
    public function testRejectsDuplicateUntilTtlExpires(): void
    {
        $window = new DeduplicationWindow(30);
        $now = new DateTimeImmutable('2026-08-02T00:00:00Z');

        self::assertTrue($window->firstSeen('evt-1', $now));
        self::assertFalse($window->firstSeen('evt-1', $now->modify('+10 seconds')));
        self::assertTrue($window->firstSeen('evt-1', $now->modify('+31 seconds')));
    }
}
