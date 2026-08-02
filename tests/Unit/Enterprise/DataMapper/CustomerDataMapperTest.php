<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\DataMapper;

use DesignPatterns\Enterprise\DataMapper\CustomerDataMapper;
use DesignPatterns\Enterprise\DataMapper\InMemoryCustomerRowGateway;
use DesignPatterns\Enterprise\Repository\Customer;
use PHPUnit\Framework\TestCase;

final class CustomerDataMapperTest extends TestCase
{
    public function test_it_maps_entity_to_row_and_back(): void
    {
        $mapper = new CustomerDataMapper(new InMemoryCustomerRowGateway());
        $mapper->save(new Customer(7, 'mapped@example.com', false));

        $customer = $mapper->find(7);

        self::assertNotNull($customer);
        self::assertSame('mapped@example.com', $customer->email);
        self::assertFalse($customer->active);
    }
}
