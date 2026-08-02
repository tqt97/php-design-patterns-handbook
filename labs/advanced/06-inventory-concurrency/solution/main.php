<?php

declare(strict_types=1);

interface InventoryConcurrencyPort
{
    public function execute(string $id): string;
}

final class InMemoryInventoryConcurrencyPort implements InventoryConcurrencyPort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '06-inventory-concurrency:' . $id . ':ok';
    }
}

final readonly class InventoryConcurrency
{
    public function __construct(private InventoryConcurrencyPort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new InventoryConcurrency(new InMemoryInventoryConcurrencyPort()))->execute('demo'), PHP_EOL;
}
