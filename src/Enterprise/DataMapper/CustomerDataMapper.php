<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\DataMapper;

use DesignPatterns\Enterprise\Repository\Customer;

final readonly class CustomerDataMapper
{
    public function __construct(private CustomerRowGateway $rows)
    {
    }

    public function find(int $id): ?Customer
    {
        $row = $this->rows->find($id);

        return $row === null
            ? null
            : new Customer($row['id'], $row['email'], $row['active']);
    }

    public function save(Customer $customer): void
    {
        $this->rows->persist([
            'id' => $customer->id,
            'email' => $customer->email,
            'active' => $customer->active,
        ]);
    }
}
