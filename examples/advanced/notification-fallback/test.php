<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new NotificationFallbackUseCase(new InMemoryChannel()))->handle('case-42');
assert($result === 'notification:case-42:ok');

echo 'PASS notification-fallback', PHP_EOL;
