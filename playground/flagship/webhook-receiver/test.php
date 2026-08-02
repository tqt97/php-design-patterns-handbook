<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new WebhookReceiverApplication(new InMemoryWebhookHandler());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'webhook-receiver:demo:ok');
assert($second === $first);

echo 'PASS webhook-receiver', PHP_EOL;
