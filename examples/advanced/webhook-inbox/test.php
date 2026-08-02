<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new WebhookInboxUseCase(new InMemoryInbox()))->handle('case-42');
assert($result === 'integration:case-42:ok');

echo 'PASS webhook-inbox', PHP_EOL;
