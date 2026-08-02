<?php

declare(strict_types=1);

require __DIR__ . '/../solution/main.php';

$app = new BookingStateMachine(new InMemoryBookingStateMachinePort());
assert($app->execute('42') === '04-booking-state-machine:42:ok');

echo 'PASS 04-booking-state-machine', PHP_EOL;
