<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new OrderWorkflowApplication(new InMemoryOrderStep());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'order-workflow:demo:ok');
assert($second === $first);

echo 'PASS order-workflow', PHP_EOL;
