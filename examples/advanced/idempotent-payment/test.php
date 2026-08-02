<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new IdempotentPaymentUseCase(new InMemoryIdempotencyStore()))->handle('case-42');
assert($result === 'payment:case-42:ok');

echo 'PASS idempotent-payment', PHP_EOL;
