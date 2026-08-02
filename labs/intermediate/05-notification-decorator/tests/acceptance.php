<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new NotificationDecorator(new InMemoryNotificationDecoratorPort());
assert($app->execute('42') === '05-notification-decorator:42:ok');

echo 'PASS 05-notification-decorator', PHP_EOL;
