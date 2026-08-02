<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Repository;

final class InMemoryCustomerRepository implements CustomerRepository
{
    /** @var array<int, Customer> */
    private array $customers = [];

    public function save(Customer $customer): void
    {
        $this->customers[$customer->id] = $customer;
    }

    public function getById(int $id): Customer
    {
        return $this->customers[$id]
            ?? throw new \OutOfBoundsException("Customer {$id} was not found.");
    }

    public function activeCustomers(): array
    {
        return array_values(array_filter(
            $this->customers,
            static fn (Customer $customer): bool => $customer->active,
        ));
    }
}
