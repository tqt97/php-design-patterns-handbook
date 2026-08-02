<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new TransactionalOutbox(new InMemoryTransactionalOutboxPort());
assert($app->execute('42') === '05-transactional-outbox:42:ok');

echo 'PASS 05-transactional-outbox', PHP_EOL;
