<?php

declare(strict_types=1);

interface BookingStateMachinePort
{
    public function execute(string $id): string;
}

final class InMemoryBookingStateMachinePort implements BookingStateMachinePort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '04-booking-state-machine:' . $id . ':ok';
    }
}

final readonly class BookingStateMachine
{
    public function __construct(private BookingStateMachinePort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new BookingStateMachine(new InMemoryBookingStateMachinePort()))->execute('demo'), PHP_EOL;
}
