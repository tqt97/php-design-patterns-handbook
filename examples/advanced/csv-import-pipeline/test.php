<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new CSVImportPipelineUseCase(new InMemoryImportStep()))->handle('case-42');
assert($result === 'import:case-42:ok');

echo 'PASS csv-import-pipeline', PHP_EOL;
