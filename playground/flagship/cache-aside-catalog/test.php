<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new CacheAsideCatalogApplication(new InMemoryProductSource());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'cache-aside-catalog:demo:ok');
assert($second === $first);

echo 'PASS cache-aside-catalog', PHP_EOL;
