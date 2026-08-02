<?php

declare(strict_types=1);

interface CustomerQueryObjectPort
{
    public function execute(string $id): string;
}

final class InMemoryCustomerQueryObjectPort implements CustomerQueryObjectPort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '06-customer-query-object:' . $id . ':ok';
    }
}

final readonly class CustomerQueryObject
{
    public function __construct(private CustomerQueryObjectPort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new CustomerQueryObject(new InMemoryCustomerQueryObjectPort()))->execute('demo'), PHP_EOL;
}
