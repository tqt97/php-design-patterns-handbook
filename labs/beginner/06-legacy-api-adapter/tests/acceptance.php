<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new LegacyAPIAdapter(new InMemoryLegacyAPIAdapterPort());
assert($app->execute('42') === '06-legacy-api-adapter:42:ok');

echo 'PASS 06-legacy-api-adapter', PHP_EOL;
