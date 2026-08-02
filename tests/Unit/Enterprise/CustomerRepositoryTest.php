<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise;

use DesignPatterns\Enterprise\Repository\Customer;
use DesignPatterns\Enterprise\Repository\InMemoryCustomerRepository;
use PHPUnit\Framework\TestCase;

final class CustomerRepositoryTest extends TestCase
{
    public function testItStoresAndFiltersCustomers(): void
    {
        $repository = new InMemoryCustomerRepository();
        $repository->save(new Customer(1, 'a@example.com'));
        $repository->save(new Customer(2, 'b@example.com', false));

        self::assertSame(1, $repository->getById(1)->id);
        self::assertCount(1, $repository->activeCustomers());
    }
}
