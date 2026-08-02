<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new ApprovalChainUseCase(new InMemoryApprover()))->handle('case-42');
assert($result === 'approval:case-42:ok');

echo 'PASS approval-chain', PHP_EOL;
