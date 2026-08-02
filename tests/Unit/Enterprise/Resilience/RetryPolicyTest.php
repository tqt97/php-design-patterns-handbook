<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Resilience;

use DesignPatterns\Enterprise\Resilience\FailureKind;
use DesignPatterns\Enterprise\Resilience\RetryPolicy;
use PHPUnit\Framework\TestCase;

final class RetryPolicyTest extends TestCase
{
    public function testRetriesIdempotentTransientFailureWithinBudget(): void
    {
        $decision = (new RetryPolicy(maxAttempts: 3))->decide(FailureKind::Transient, 1, true);

        self::assertTrue($decision->shouldRetry);
        self::assertFalse($decision->requiresReconciliation);
    }

    public function testAmbiguousOutcomeRequiresReconciliation(): void
    {
        $decision = (new RetryPolicy())->decide(FailureKind::Ambiguous, 1, true);

        self::assertFalse($decision->shouldRetry);
        self::assertTrue($decision->requiresReconciliation);
    }

    public function testPermanentFailureStopsImmediately(): void
    {
        $decision = (new RetryPolicy())->decide(FailureKind::Permanent, 1, true);

        self::assertFalse($decision->shouldRetry);
        self::assertFalse($decision->requiresReconciliation);
    }
}
