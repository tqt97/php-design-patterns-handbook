<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new FileExportFactory(new InMemoryFileExportFactoryPort());
assert($app->execute('42') === '05-file-export-factory:42:ok');

echo 'PASS 05-file-export-factory', PHP_EOL;
