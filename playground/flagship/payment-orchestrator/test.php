<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new PaymentOrchestratorApplication(new InMemoryPaymentGateway());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'payment-orchestrator:demo:ok');
assert($second === $first);

echo 'PASS payment-orchestrator', PHP_EOL;
