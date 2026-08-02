<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\ServiceLayer;

use DesignPatterns\Enterprise\Repository\InMemoryCustomerRepository;
use DesignPatterns\Enterprise\ServiceLayer\CustomerRegistrationService;
use PHPUnit\Framework\TestCase;

final class CustomerRegistrationServiceTest extends TestCase
{
    public function test_it_registers_and_persists_a_customer(): void
    {
        $repository = new InMemoryCustomerRepository();
        $service = new CustomerRegistrationService($repository);

        $customer = $service->register(10, 'new@example.com');

        self::assertSame('new@example.com', $customer->email);
        self::assertSame($customer, $repository->getById(10));
    }

    public function test_it_rejects_duplicate_ids(): void
    {
        $repository = new InMemoryCustomerRepository();
        $service = new CustomerRegistrationService($repository);
        $service->register(10, 'first@example.com');

        $this->expectException(\DomainException::class);
        $service->register(10, 'second@example.com');
    }
}
