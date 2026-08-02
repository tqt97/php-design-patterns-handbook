<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Repository;

interface CustomerRepository
{
    public function save(Customer $customer): void;

    public function getById(int $id): Customer;

    /** @return list<Customer> */
    public function activeCustomers(): array;
}
