<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new NotificationRouterApplication(new InMemoryNotificationChannel());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'notification-router:demo:ok');
assert($second === $first);

echo 'PASS notification-router', PHP_EOL;
