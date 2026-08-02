<?php

declare(strict_types=1);

$base = 30000;
$withMilk = true;
$withPearl = true;
$total = $base + ($withMilk ? 5000 : 0) + ($withPearl ? 7000 : 0);
echo $total . PHP_EOL;
