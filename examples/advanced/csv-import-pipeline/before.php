<?php

declare(strict_types=1);

function process_import(string $id): string
{
    if ($id === '') {
        throw new InvalidArgumentException('ID must not be empty.');
    }

    // Policy, persistence và output bị trộn trong cùng function.
    return 'import:' . $id . ':ok';
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo process_import('demo-1'), PHP_EOL;
}
