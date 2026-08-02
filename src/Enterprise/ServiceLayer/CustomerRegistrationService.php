<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\ServiceLayer;

use DesignPatterns\Enterprise\Repository\Customer;
use DesignPatterns\Enterprise\Repository\CustomerRepository;

final class CustomerRegistrationService
{
    public function __construct(private CustomerRepository $customers)
    {
    }

    public function register(int $id, string $email): Customer
    {
        try {
            $this->customers->getById($id);
            throw new \DomainException('Customer ID already exists.');
        } catch (\RuntimeException) {
            // Expected when the repository cannot find the customer.
        }

        $customer = new Customer($id, $email);
        $this->customers->save($customer);

        return $customer;
    }
}
