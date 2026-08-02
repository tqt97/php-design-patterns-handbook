<?php

declare(strict_types=1);

function confirm_booking(string $id): string
{
    if ($id === '') {
        throw new InvalidArgumentException('ID must not be empty.');
    }

    // Policy, persistence và output bị trộn trong cùng function.
    return 'booking:' . $id . ':ok';
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo confirm_booking('demo-1'), PHP_EOL;
}
