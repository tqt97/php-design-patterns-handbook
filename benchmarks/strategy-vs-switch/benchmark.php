<?php

declare(strict_types=1);
require dirname(__DIR__) . '/Benchmark.php';

interface ShippingStrategy
{
    public function fee(int $weight): int;
}
final class StandardShipping implements ShippingStrategy
{
    public function fee(int $weight): int
    {
        return 20_000 + $weight * 1_000;
    }
}
final class ExpressShipping implements ShippingStrategy
{
    public function fee(int $weight): int
    {
        return 35_000 + $weight * 2_000;
    }
}

$standard = new StandardShipping();
$express = new ExpressShipping();
$type = 0;
$weight = 3;

$results = [
    'match expression' => Benchmark::measure(function () use (&$type, $weight): int {
        $type ^= 1;
        return match ($type) {
            0 => 20_000 + $weight * 1_000,
            1 => 35_000 + $weight * 2_000,
        };
    }),
    'strategy interface' => Benchmark::measure(function () use (&$type, $weight, $standard, $express): int {
        $type ^= 1;
        return ($type === 0 ? $standard : $express)->fee($weight);
    }),
];
Benchmark::report('Strategy vs Switch', $results);
