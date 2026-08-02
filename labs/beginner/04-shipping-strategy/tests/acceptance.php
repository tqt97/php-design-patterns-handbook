<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new ShippingStrategy(new InMemoryShippingStrategyPort());
assert($app->execute('42') === '04-shipping-strategy:42:ok');

echo 'PASS 04-shipping-strategy', PHP_EOL;
