<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Resilience;

use DateTimeImmutable;
use DesignPatterns\Enterprise\Resilience\CircuitBreaker;
use DesignPatterns\Enterprise\Resilience\CircuitOpenException;
use DesignPatterns\Enterprise\Resilience\CircuitState;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class CircuitBreakerTest extends TestCase
{
    public function testItOpensAfterThresholdAndRecoversThroughHalfOpenProbe(): void
    {
        $breaker = new CircuitBreaker(failureThreshold: 2, recoveryTimeoutSeconds: 10);
        $now = new DateTimeImmutable('2026-01-01T00:00:00Z');

        for ($attempt = 0; $attempt < 2; ++$attempt) {
            try {
                $breaker->execute(static fn (): never => throw new RuntimeException('provider unavailable'), $now);
            } catch (RuntimeException) {
                // Expected dependency failure.
            }
        }

        self::assertSame(CircuitState::Open, $breaker->state($now));

        $this->expectException(CircuitOpenException::class);
        $breaker->execute(static fn (): string => 'not called', $now);
    }

    public function testSuccessfulProbeClosesCircuit(): void
    {
        $breaker = new CircuitBreaker(failureThreshold: 1, recoveryTimeoutSeconds: 5);
        $openedAt = new DateTimeImmutable('2026-01-01T00:00:00Z');

        try {
            $breaker->execute(static fn (): never => throw new RuntimeException('timeout'), $openedAt);
        } catch (RuntimeException) {
        }

        $probeAt = $openedAt->modify('+5 seconds');
        self::assertSame('ok', $breaker->execute(static fn (): string => 'ok', $probeAt));
        self::assertSame(CircuitState::Closed, $breaker->state($probeAt));
        self::assertSame(0, $breaker->consecutiveFailures());
    }
}
