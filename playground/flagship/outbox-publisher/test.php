<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new OutboxPublisherApplication(new InMemoryPublisher());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'outbox-publisher:demo:ok');
assert($second === $first);

echo 'PASS outbox-publisher', PHP_EOL;
