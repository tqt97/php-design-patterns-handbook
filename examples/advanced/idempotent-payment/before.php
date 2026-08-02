<?php

declare(strict_types=1);

function charge_payment(string $id): string
{
    if ($id === '') {
        throw new InvalidArgumentException('ID must not be empty.');
    }

    // Policy, persistence và output bị trộn trong cùng function.
    return 'payment:' . $id . ':ok';
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo charge_payment('demo-1'), PHP_EOL;
}
