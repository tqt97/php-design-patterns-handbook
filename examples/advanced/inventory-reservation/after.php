<?php

declare(strict_types=1);

interface ReservationPolicy
{
    public function reserve(string $id): string;
}

final class InMemoryReservationPolicy implements ReservationPolicy
{
    public function reserve(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'inventory:' . $id . ':ok';
    }
}

final readonly class InventoryReservationUseCase
{
    public function __construct(private ReservationPolicy $component) {}

    public function handle(string $id): string
    {
        return $this->component->reserve($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new InventoryReservationUseCase(new InMemoryReservationPolicy()))->handle('demo-1'), PHP_EOL;
}
