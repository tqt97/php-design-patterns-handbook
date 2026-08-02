<?php

declare(strict_types=1);

final class LegacyGateway
{
	public function makePayment(int $cents): string
	{
		return 'legacy-' . $cents;
	}
}
final class CheckoutService
{
	public function __construct(private LegacyGateway $gateway)
	{
	}
	public function checkout(int $amountVnd): string
	{
		return $this->gateway->makePayment($amountVnd * 100);
	}
}
