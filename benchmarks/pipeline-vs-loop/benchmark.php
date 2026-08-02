<?php

declare(strict_types=1);
require dirname(__DIR__) . '/Benchmark.php';

$steps = [
    static fn(int $value): int => $value + 1,
    static fn(int $value): int => $value * 2,
    static fn(int $value): int => $value - 3,
];
$pipeline = array_reduce(
    array_reverse($steps),
    static fn(callable $next, callable $step): callable => static fn(int $value): int => $next($step($value)),
    static fn(int $value): int => $value,
);
$results = [
    'foreach steps' => Benchmark::measure(function () use ($steps): int {
        $value = 10;
        foreach ($steps as $step) {
            $value = $step($value); }return $value; }),
    'nested pipeline' => Benchmark::measure(fn(): int => $pipeline(10)),
];
Benchmark::report('Pipeline vs Loop', $results);
