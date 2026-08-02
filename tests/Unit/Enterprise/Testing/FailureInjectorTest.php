<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Testing;

use DesignPatterns\Enterprise\Testing\FailureInjector;
use DesignPatterns\Enterprise\Testing\InjectedFailure;
use PHPUnit\Framework\TestCase;

final class FailureInjectorTest extends TestCase
{
    public function testSameSeedProducesSameFailureCall(): void
    {
        $first = $this->firstFailureCall(new FailureInjector(42));
        $second = $this->firstFailureCall(new FailureInjector(42));
        self::assertSame($first, $second);
    }

    private function firstFailureCall(FailureInjector $injector): int
    {
        for ($call = 1; $call <= 100; $call++) {
            try {
                $injector->checkpoint('payment.after-provider', 5);
            } catch (InjectedFailure $failure) {
                return $failure->call;
            }
        }
        self::fail('Expected deterministic failure.');
    }
}
