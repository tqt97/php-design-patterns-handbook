<?php

declare(strict_types=1);

interface DiscountRule
{
	public function discount(Customer $customer, Cart $cart): int;
}
final class Customer
{
	public function __construct(public bool $vip)
	{
	}
}
final class Cart
{
	public function __construct(public int $total)
	{
	}
}
final class VipDiscount implements DiscountRule
{
	public function discount(Customer $customer, Cart $cart): int
	{
		return $customer->vip ? (int) ($cart->total * 0.1) : 0;
	}
}
final class MinimumOrderDiscount implements DiscountRule
{
	public function discount(Customer $customer, Cart $cart): int
	{
		return $cart->total >= 1000000 ? 50000 : 0;
	}
}
final class BestDiscount implements DiscountRule
{
	public function __construct(private array $rules)
	{
	}
	public function discount(Customer $customer, Cart $cart): int
	{
		return max(array_map(fn(DiscountRule $rule) => $rule->discount($customer, $cart), $this->rules));
	}
}
