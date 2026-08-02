<?php

declare(strict_types=1);

final class FailurePlan
{
    public function __construct(private readonly int $seed) {}
    public function fails(string $checkpoint, int $call): bool
    {
        return (crc32($checkpoint . ':' . $call . ':' . $this->seed) % 5) === 0;
    }
}

$seed = isset($argv[1]) ? (int) $argv[1] : 42;
$plan = new FailurePlan($seed);
$ambiguous = false;
for ($call = 1; $call <= 20; $call++) {
    $providerSucceeded = true;
    if ($providerSucceeded && $plan->fails('payment.after-provider-before-persist', $call)) {
        $ambiguous = true;
        echo "INJECTED ambiguous outcome call={$call} seed={$seed}\n";
        break;
    }
}
assert($ambiguous, 'Seed must create at least one deterministic ambiguous outcome.');
echo "PASS reconciliation required\n";
