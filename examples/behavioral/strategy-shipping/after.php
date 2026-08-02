<?php

declare(strict_types=1);

interface ShippingStrategy
{
	public function fee(int $weight): int;
}
final class GrabShipping implements ShippingStrategy
{
	public function fee(int $weight): int
	{
		return 15000 + $weight * 2000;
	}
}
final class GhnShipping implements ShippingStrategy
{
	public function fee(int $weight): int
	{
		return 12000 + $weight * 2500;
	}
}
final class Checkout
{
	public function __construct(private ShippingStrategy $shipping)
	{
	}
	public function shippingFee(int $weight): int
	{
		return $this->shipping->fee($weight);
	}
}
echo (new Checkout(new GrabShipping()))->shippingFee(3) . PHP_EOL;
