<?php

declare(strict_types=1);

interface ShippingStrategyPort
{
    public function execute(string $id): string;
}

final class InMemoryShippingStrategyPort implements ShippingStrategyPort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '04-shipping-strategy:' . $id . ':ok';
    }
}

final readonly class ShippingStrategy
{
    public function __construct(private ShippingStrategyPort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new ShippingStrategy(new InMemoryShippingStrategyPort()))->execute('demo'), PHP_EOL;
}
