<?php

declare(strict_types=1);
require dirname(__DIR__) . '/Benchmark.php';

final class Customer
{
    public function __construct(public int $id, public string $name)
    {
    }
}
interface CustomerRepository
{
    public function find(int $id): ?Customer;
}
final class InMemoryCustomerRepository implements CustomerRepository
{
    /** @param array<int, Customer> $customers */
    public function __construct(private array $customers)
    {
    }
    public function find(int $id): ?Customer
    {
        return $this->customers[$id] ?? null;
    }
}
$customers = [1 => new Customer(1, 'An')];
$repository = new InMemoryCustomerRepository($customers);
$results = [
    'direct array' => Benchmark::measure(fn(): ?Customer => $customers[1] ?? null),
    'repository method' => Benchmark::measure(fn(): ?Customer => $repository->find(1)),
];
Benchmark::report('Repository Overhead', $results);
