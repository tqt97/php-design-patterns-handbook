<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new AuditDecoratorUseCase(new InMemoryAction()))->handle('case-42');
assert($result === 'audit:case-42:ok');

echo 'PASS audit-decorator', PHP_EOL;
