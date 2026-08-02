<?php

declare(strict_types=1);

interface Specification
{
	public function isSatisfiedBy(Customer $customer): bool;
}
final class Customer
{
	public function __construct(public bool $vip, public int $totalSpent)
	{
	}
}
final class VipCustomer implements Specification
{
	public function isSatisfiedBy(Customer $customer): bool
	{
		return $customer->vip;
	}
}
final class MinimumSpent implements Specification
{
	public function __construct(private int $minimum)
	{
	}
	public function isSatisfiedBy(Customer $customer): bool
	{
		return $customer->totalSpent >= $this->minimum;
	}
}
final class AndSpecification implements Specification
{
	public function __construct(private Specification $left, private Specification $right)
	{
	}
	public function isSatisfiedBy(Customer $customer): bool
	{
		return $this->left->isSatisfiedBy($customer) && $this->right->isSatisfiedBy($customer);
	}
}
$rule = new AndSpecification(new VipCustomer(), new MinimumSpent(500000));
echo $rule->isSatisfiedBy(new Customer(true, 600000)) ? 'Eligible' : 'Not eligible';
