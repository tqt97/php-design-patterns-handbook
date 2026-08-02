<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new IdempotentPayment(new InMemoryIdempotentPaymentPort());
assert($app->execute('42') === '04-idempotent-payment:42:ok');

echo 'PASS 04-idempotent-payment', PHP_EOL;
