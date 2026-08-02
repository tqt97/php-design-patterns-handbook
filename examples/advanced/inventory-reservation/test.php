<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new InventoryReservationUseCase(new InMemoryReservationPolicy()))->handle('case-42');
assert($result === 'inventory:case-42:ok');

echo 'PASS inventory-reservation', PHP_EOL;
