<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience\Backpressure;

final readonly class EnqueueDecision
{
    public function __construct(
        public bool $accepted,
        public int $queueSize,
        public ?string $reason = null,
    ) {
    }
}
