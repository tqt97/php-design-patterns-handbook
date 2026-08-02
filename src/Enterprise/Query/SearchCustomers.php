<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Query;

use DesignPatterns\Enterprise\Repository\Customer;

final class SearchCustomers
{
    /** @param list<Customer> $customers
     *  @return list<Customer>
     */
    public function execute(array $customers, CustomerSearchCriteria $criteria): array
    {
        $filtered = array_filter($customers, static function (Customer $customer) use ($criteria): bool {
            if ($criteria->active !== null && $customer->active !== $criteria->active) {
                return false;
            }

            return $criteria->emailContains === null
                || str_contains(strtolower($customer->email), strtolower($criteria->emailContains));
        });

        return array_slice(array_values($filtered), 0, $criteria->limit);
    }
}
