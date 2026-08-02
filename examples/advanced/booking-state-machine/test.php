<?php

declare(strict_types=1);

require __DIR__ . '/after.php';

$result = (new BookingStateMachineUseCase(new InMemoryBookingState()))->handle('case-42');
assert($result === 'booking:case-42:ok');

echo 'PASS booking-state-machine', PHP_EOL;
