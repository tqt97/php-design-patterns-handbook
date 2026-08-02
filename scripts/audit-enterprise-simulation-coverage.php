<?php

declare(strict_types=1);

$root = dirname(__DIR__);

$artifacts = [
    'Circuit Breaker' => [
        'src/Enterprise/Resilience/CircuitBreaker.php',
        'tests/Unit/Enterprise/Resilience/CircuitBreakerTest.php',
        'docs/09-expert-practice/19-circuit-breaker-operability.md',
    ],
    'Bulkhead' => [
        'src/Enterprise/Resilience/Bulkhead.php',
        'tests/Unit/Enterprise/Resilience/BulkheadTest.php',
        'docs/09-expert-practice/18-bulkhead-and-capacity-isolation.md',
    ],
    'Rate Limiter' => [
        'src/Enterprise/Resilience/RateLimiter/FixedWindowRateLimiter.php',
        'tests/Unit/Enterprise/Resilience/RateLimiter/FixedWindowRateLimiterTest.php',
        'docs/09-expert-practice/31-rate-limiting-and-admission-control.md',
    ],
    'Backpressure' => [
        'src/Enterprise/Resilience/Backpressure/BoundedWorkQueue.php',
        'tests/Unit/Enterprise/Resilience/Backpressure/BoundedWorkQueueTest.php',
        'docs/09-expert-practice/32-backpressure-and-bounded-queues.md',
    ],
    'Failure Injection' => [
        'src/Enterprise/Testing/FailureInjector.php',
        'tests/Unit/Enterprise/Testing/FailureInjectorTest.php',
        'expert-labs/failure-injection/README.md',
    ],
    'Dual Run' => [
        'src/Enterprise/Migration/DualRunComparator.php',
        'tests/Unit/Enterprise/Migration/DualRunComparatorTest.php',
        'docs/09-expert-practice/28-migration-rehearsal-dual-run.md',
    ],
    'Message Deduplication' => [
        'src/Enterprise/Messaging/DeduplicationWindow.php',
        'tests/Unit/Enterprise/Messaging/DeduplicationWindowTest.php',
        'docs/09-expert-practice/20-enterprise-pattern-operability.md',
    ],
];

$errors = [];
foreach ($artifacts as $capability => $paths) {
    foreach ($paths as $path) {
        if (! is_file($root . '/' . $path)) {
            $errors[] = sprintf('%s thiếu artifact: %s', $capability, $path);
        }
    }
}

$smoke = file_get_contents($root . '/scripts/run-source-smoke-tests.php');
foreach ([
    'CircuitBreaker',
    'Bulkhead',
    'FixedWindowRateLimiter',
    'BoundedWorkQueue',
    'FailureInjector',
    'DualRunComparator',
    'DeduplicationWindow',
] as $className) {
    if (! str_contains($smoke, $className)) {
        $errors[] = sprintf('Source smoke test chưa mô phỏng %s.', $className);
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL enterprise simulation coverage:\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo sprintf(
    "PASS enterprise simulation coverage: %d capabilities có source, test, tài liệu và smoke evidence.\n",
    count($artifacts),
);
