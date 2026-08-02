<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new ApprovalWorkflowApplication(new InMemoryApprovalPolicy());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'approval-workflow:demo:ok');
assert($second === $first);

echo 'PASS approval-workflow', PHP_EOL;
