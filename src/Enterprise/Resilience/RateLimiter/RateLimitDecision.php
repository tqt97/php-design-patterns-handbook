<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience\RateLimiter;

final readonly class RateLimitDecision
{
    public function __construct(
        public bool $allowed,
        public int $remaining,
        public int $retryAfterSeconds,
    ) {
    }
}
