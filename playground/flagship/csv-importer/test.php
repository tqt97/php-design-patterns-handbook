<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new CSVImporterApplication(new InMemoryImportStage());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'csv-importer:demo:ok');
assert($second === $first);

echo 'PASS csv-importer', PHP_EOL;
