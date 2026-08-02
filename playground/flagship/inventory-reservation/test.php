<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new InventoryReservationApplication(new InMemoryStockPort());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'inventory-reservation:demo:ok');
assert($second === $first);

echo 'PASS inventory-reservation', PHP_EOL;
