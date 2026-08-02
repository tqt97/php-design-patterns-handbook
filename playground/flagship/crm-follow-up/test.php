<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new CRMFollowUpApplication(new InMemoryLeadPolicy());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'crm-follow-up:demo:ok');
assert($second === $first);

echo 'PASS crm-follow-up', PHP_EOL;
