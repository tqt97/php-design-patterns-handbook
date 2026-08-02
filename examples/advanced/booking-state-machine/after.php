<?php

declare(strict_types=1);

interface BookingState
{
    public function confirm(string $id): string;
}

final class InMemoryBookingState implements BookingState
{
    public function confirm(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'booking:' . $id . ':ok';
    }
}

final readonly class BookingStateMachineUseCase
{
    public function __construct(private BookingState $component)
    {
    }

    public function handle(string $id): string
    {
        return $this->component->confirm($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new BookingStateMachineUseCase(new InMemoryBookingState()))->handle('demo-1'), PHP_EOL;
}
