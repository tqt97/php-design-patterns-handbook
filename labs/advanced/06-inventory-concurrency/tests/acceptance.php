<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new InventoryConcurrency(new InMemoryInventoryConcurrencyPort());
assert($app->execute('42') === '06-inventory-concurrency:42:ok');

echo 'PASS 06-inventory-concurrency', PHP_EOL;
