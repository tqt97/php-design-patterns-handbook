<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new FeatureFlagRolloutApplication(new InMemoryVariant());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'feature-flag-rollout:demo:ok');
assert($second === $first);

echo 'PASS feature-flag-rollout', PHP_EOL;
