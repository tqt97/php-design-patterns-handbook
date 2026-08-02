<?php

declare(strict_types=1);

interface StockPort
{
    public function reserve(string $id): string;
}

final class InMemoryStockPort implements StockPort
{
    /** @var array<string, string> */
    private array $results = [];

    public function reserve(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'inventory-reservation:' . $id . ':ok';
    }
}

final readonly class InventoryReservationApplication
{
    public function __construct(private StockPort $port) {}

    public function run(string $id): string
    {
        return $this->port->reserve($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new InventoryReservationApplication(new InMemoryStockPort());
    echo $app->run('demo'), PHP_EOL;
}
