<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience;

final readonly class RetryPolicy
{
    public function __construct(private int $maxAttempts = 3)
    {
        if ($maxAttempts < 1) {
            throw new \InvalidArgumentException('maxAttempts must be at least 1.');
        }
    }

    public function decide(FailureKind $failure, int $attempt, bool $operationIsIdempotent): RetryDecision
    {
        if ($attempt < 1) {
            throw new \InvalidArgumentException('attempt must be at least 1.');
        }

        return match ($failure) {
            FailureKind::Permanent => RetryDecision::stop('Permanent failures require correction, not retry.'),
            FailureKind::Ambiguous => RetryDecision::reconcile('The external outcome is unknown; reconcile before another side effect.'),
            FailureKind::Transient => $this->transientDecision($attempt, $operationIsIdempotent),
        };
    }

    private function transientDecision(int $attempt, bool $operationIsIdempotent): RetryDecision
    {
        if (! $operationIsIdempotent) {
            return RetryDecision::reconcile('Retry is unsafe because the operation is not idempotent.');
        }

        if ($attempt >= $this->maxAttempts) {
            return RetryDecision::stop('Retry budget exhausted.');
        }

        return RetryDecision::retry('Transient failure within the retry budget.');
    }
}
