<?php

declare(strict_types=1);

function send_notification(string $id): string
{
    if ($id === '') {
        throw new InvalidArgumentException('ID must not be empty.');
    }

    // Policy, persistence và output bị trộn trong cùng function.
    return 'notification:' . $id . ':ok';
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo send_notification('demo-1'), PHP_EOL;
}
