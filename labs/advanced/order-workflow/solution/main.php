<?php

declare(strict_types=1);

interface OrderState
{
	public function pay(Order $order): void;
	public function ship(Order $order): void;
}
final class PendingState implements OrderState
{
	public function pay(Order $order): void
	{
		$order->changeState(new PaidState());
	}
	public function ship(Order $order): void
	{
		throw new RuntimeException('Pay first');
	}
}
final class PaidState implements OrderState
{
	public function pay(Order $order): void
	{
	}
	public function ship(Order $order): void
	{
		$order->changeState(new ShippedState());
	}
}
final class ShippedState implements OrderState
{
	public function pay(Order $order): void
	{
	}
	public function ship(Order $order): void
	{
	}
}
final class Order
{
	public function __construct(private OrderState $state)
	{
	}
	public function pay(): void
	{
		$this->state->pay($this);
	}
	public function ship(): void
	{
		$this->state->ship($this);
	}
	public function changeState(OrderState $state): void
	{
		$this->state = $state;
	}
	public function state(): string
	{
		return $this->state::class;
	}
}
