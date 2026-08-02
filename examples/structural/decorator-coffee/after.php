<?php

declare(strict_types=1);

interface Drink
{
	public function price(): int;
	public function description(): string;
}
final class Coffee implements Drink
{
	public function price(): int
	{
		return 30000;
	}
	public function description(): string
	{
		return 'Coffee';
	}
}
abstract class DrinkDecorator implements Drink
{
	public function __construct(protected Drink $drink)
	{
	}
}
final class Milk extends DrinkDecorator
{
	public function price(): int
	{
		return $this->drink->price() + 5000;
	}
	public function description(): string
	{
		return $this->drink->description() . ', milk';
	}
}
final class Pearl extends DrinkDecorator
{
	public function price(): int
	{
		return $this->drink->price() + 7000;
	}
	public function description(): string
	{
		return $this->drink->description() . ', pearl';
	}
}
$drink = new Pearl(new Milk(new Coffee()));
echo $drink->description() . ': ' . $drink->price() . PHP_EOL;
