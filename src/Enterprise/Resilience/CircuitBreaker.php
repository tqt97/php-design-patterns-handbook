<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience;

use DateTimeImmutable;
use RuntimeException;
use Throwable;

final class CircuitBreaker
{
    private CircuitState $state = CircuitState::Closed;
    private int $consecutiveFailures = 0;
    private ?DateTimeImmutable $openedAt = null;

    public function __construct(
        private readonly int $failureThreshold = 3,
        private readonly int $recoveryTimeoutSeconds = 30,
    ) {
        if ($failureThreshold < 1 || $recoveryTimeoutSeconds < 1) {
            throw new RuntimeException('Circuit breaker limits must be positive.');
        }
    }

    /** @template T @param callable(): T $operation @return T */
    public function execute(callable $operation, DateTimeImmutable $now): mixed
    {
        $this->refreshState($now);

        if ($this->state === CircuitState::Open) {
            throw new CircuitOpenException('Circuit is open; call is rejected before reaching the dependency.');
        }

        try {
            $result = $operation();
            $this->recordSuccess();

            return $result;
        } catch (Throwable $failure) {
            $this->recordFailure($now);
            throw $failure;
        }
    }

    public function state(DateTimeImmutable $now): CircuitState
    {
        $this->refreshState($now);

        return $this->state;
    }

    public function consecutiveFailures(): int
    {
        return $this->consecutiveFailures;
    }

    private function refreshState(DateTimeImmutable $now): void
    {
        if ($this->state !== CircuitState::Open || $this->openedAt === null) {
            return;
        }

        if ($now->getTimestamp() - $this->openedAt->getTimestamp() >= $this->recoveryTimeoutSeconds) {
            $this->state = CircuitState::HalfOpen;
        }
    }

    private function recordSuccess(): void
    {
        $this->state = CircuitState::Closed;
        $this->consecutiveFailures = 0;
        $this->openedAt = null;
    }

    private function recordFailure(DateTimeImmutable $now): void
    {
        ++$this->consecutiveFailures;

        if ($this->state === CircuitState::HalfOpen || $this->consecutiveFailures >= $this->failureThreshold) {
            $this->state = CircuitState::Open;
            $this->openedAt = $now;
        }
    }
}
