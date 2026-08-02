<?php

declare(strict_types=1);

final class Customer
{
	public function __construct(public int $id, public bool $active)
	{
	}
	public function activate(): void
	{
		$this->active = true;
	}
}
interface CustomerRepository
{
	public function byId(int $id): Customer;
	public function save(Customer $customer): void;
}
final class CustomerService
{
	public function __construct(private CustomerRepository $customers)
	{
	}
	public function activate(int $id): void
	{
		$customer = $this->customers->byId($id);
		$customer->activate();
		$this->customers->save($customer);
	}
}
echo "Repository boundary keeps persistence outside business policy.\n";
