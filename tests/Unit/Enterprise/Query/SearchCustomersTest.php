<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Query;

use DesignPatterns\Enterprise\Query\CustomerSearchCriteria;
use DesignPatterns\Enterprise\Query\SearchCustomers;
use DesignPatterns\Enterprise\Repository\Customer;
use PHPUnit\Framework\TestCase;

final class SearchCustomersTest extends TestCase
{
    public function testItFiltersByStatusAndEmailWithoutLeakingPersistenceDetails(): void
    {
        $result = (new SearchCustomers())->execute([
            new Customer(1, 'alice@example.com'),
            new Customer(2, 'bob@example.com', false),
        ], new CustomerSearchCriteria(emailContains: 'alice', active: true));

        self::assertCount(1, $result);
        self::assertSame(1, $result[0]->id);
    }
}
