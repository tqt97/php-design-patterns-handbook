<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new CustomerQueryObject(new InMemoryCustomerQueryObjectPort());
assert($app->execute('42') === '06-customer-query-object:42:ok');

echo 'PASS 06-customer-query-object', PHP_EOL;
