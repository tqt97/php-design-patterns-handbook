<?php

declare(strict_types=1);

final class Rng
{
    public function __construct(private int $state) {}
    public function int(int $min, int $max): int
    {
        $this->state = (1103515245 * $this->state + 12345) & 0x7fffffff;
        return $min + ($this->state % ($max - $min + 1));
    }
}

$seed = isset($argv[1]) ? (int) $argv[1] : 20260802;
$cases = isset($argv[2]) ? max(1, (int) $argv[2]) : 200;
$rng = new Rng($seed);

for ($i = 0; $i < $cases; $i++) {
    $a = $rng->int(0, 1_000_000);
    $b = $rng->int(0, 1_000_000);
    assert(($a + $b) - $b === $a, "Money property failed seed={$seed} case={$i}");

    $price = $rng->int(0, 1_000_000);
    $percent = $rng->int(0, 100);
    $discounted = $price - intdiv($price * $percent, 100);
    assert($discounted >= 0 && $discounted <= $price, "Discount property failed seed={$seed} case={$i}");

    $onHand = $rng->int(0, 1000);
    $reserved = $rng->int(0, $onHand);
    $available = $onHand - $reserved;
    assert($onHand === $available + $reserved && $available >= 0, "Stock property failed seed={$seed} case={$i}");

    $startA = $rng->int(0, 1000);
    $endA = $startA + $rng->int(1, 100);
    $startB = $rng->int(0, 1000);
    $endB = $startB + $rng->int(1, 100);
    $overlap = $startA < $endB && $startB < $endA;
    $separated = $endA <= $startB || $endB <= $startA;
    assert($overlap !== $separated, "Booking property failed seed={$seed} case={$i}");
}

echo "PASS property workbook seed={$seed} cases={$cases}\n";
