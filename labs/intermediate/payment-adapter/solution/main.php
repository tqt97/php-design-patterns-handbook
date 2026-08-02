<?php

declare(strict_types=1);

interface PaymentGateway
{
	public function charge(int $amountVnd): string;
}
final class LegacyGateway
{
	public function makePayment(int $cents): string
	{
		return 'legacy-' . $cents;
	}
}
final class LegacyGatewayAdapter implements PaymentGateway
{
	public function __construct(private LegacyGateway $gateway)
	{
	}
	public function charge(int $amountVnd): string
	{
		return $this->gateway->makePayment($amountVnd * 100);
	}
}
final class CheckoutService
{
	public function __construct(private PaymentGateway $gateway)
	{
	}
	public function checkout(int $amountVnd): string
	{
		return $this->gateway->charge($amountVnd);
	}
}
