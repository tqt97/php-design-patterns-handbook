<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new CRMQueryObjectUseCase(new InMemoryCustomerQuery()))->handle('case-42');
assert($result === 'crm:case-42:ok');

echo 'PASS crm-query-object', PHP_EOL;
