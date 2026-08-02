<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience;

final readonly class RetryDecision
{
    private function __construct(
        public bool $shouldRetry,
        public bool $requiresReconciliation,
        public string $reason,
    ) {
    }

    public static function retry(string $reason): self
    {
        return new self(true, false, $reason);
    }

    public static function stop(string $reason): self
    {
        return new self(false, false, $reason);
    }

    public static function reconcile(string $reason): self
    {
        return new self(false, true, $reason);
    }
}
