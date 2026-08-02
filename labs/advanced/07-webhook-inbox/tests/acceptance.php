<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new WebhookInbox(new InMemoryWebhookInboxPort());
assert($app->execute('42') === '07-webhook-inbox:42:ok');

echo 'PASS 07-webhook-inbox', PHP_EOL;
