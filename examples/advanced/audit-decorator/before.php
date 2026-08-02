<?php

declare(strict_types=1);

function execute_audit(string $id): string
{
    if ($id === '') {
        throw new InvalidArgumentException('ID must not be empty.');
    }

    // Policy, persistence và output bị trộn trong cùng function.
    return 'audit:' . $id . ':ok';
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo execute_audit('demo-1'), PHP_EOL;
}
