<?php

declare(strict_types=1);

$carrier = $argv[1] ?? 'grab';
$weight = 3;
$fee = match ($carrier) {
    'grab' => 15000 + $weight * 2000,
    'ghn' => 12000 + $weight * 2500,
    default => throw new InvalidArgumentException('Unsupported carrier'),
};
echo $fee . PHP_EOL;
