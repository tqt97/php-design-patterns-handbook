<?php

declare(strict_types=1);

require __DIR__ . '/index.php';

$app = new BookingConsoleApplication(new InMemoryBookingRepository());
$first = $app->run('demo');
$second = $app->run('demo');
assert($first === 'booking-console:demo:ok');
assert($second === $first);

echo 'PASS booking-console', PHP_EOL;
