<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

$requiredTopicTokens = [
    'examples/creational/factory-exporter/README.md' => ['factory', 'export'],
    'production/order-management-system/modules/order-state.md' => ['order', 'state', 'transition'],
    'docs/09-expert-practice/31-rate-limiting-and-admission-control.md' => ['rate', 'limit', 'budget'],
];

foreach ($requiredTopicTokens as $relative => $tokens) {
    $path = $root . '/' . $relative;
    $content = is_file($path) ? strtolower((string) file_get_contents($path)) : '';
    if ($content === '') {
        $errors[] = "Missing required topic file: {$relative}";
        continue;
    }
    foreach ($tokens as $token) {
        if (! str_contains($content, $token)) {
            $errors[] = "{$relative} does not contain topic token: {$token}";
        }
    }
}

$sourceToTest = [
    'src/Enterprise/Resilience/RateLimiter/FixedWindowRateLimiter.php' => 'tests/Unit/Enterprise/Resilience/RateLimiter/FixedWindowRateLimiterTest.php',
    'src/Enterprise/Resilience/CircuitBreaker.php' => 'tests/Unit/Enterprise/Resilience/CircuitBreakerTest.php',
    'src/Enterprise/Messaging/DeduplicationWindow.php' => 'tests/Unit/Enterprise/Messaging/DeduplicationWindowTest.php',
];

foreach ($sourceToTest as $source => $test) {
    if (! is_file($root . '/' . $source) || ! is_file($root . '/' . $test)) {
        $errors[] = "Missing source/test evidence: {$source} -> {$test}";
    }
}

foreach (['benchmarks', 'cheatsheets', 'decisions', 'docs', 'examples', 'exercises', 'framework-integration', 'handbook', 'interviews', 'kata', 'labs', 'learning-path', 'playground', 'production', 'training'] as $directory) {
    if (! is_dir($root . '/' . $directory)) {
        $errors[] = "Missing learning directory: {$directory}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FAIL topic/code alignment audit\n- " . implode("\n- ", $errors) . "\n");
    exit(1);
}

echo "PASS topic/code alignment audit\n";
