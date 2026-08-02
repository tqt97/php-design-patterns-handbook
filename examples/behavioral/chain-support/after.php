<?php

declare(strict_types=1);

abstract class Handler
{
	private ?Handler $next = null;
	public function next(Handler $next): Handler
	{
		$this->next = $next;
		return $next;
	}
	protected function forward(string $message): string
	{
		return $this->next?->handle($message) ?? 'General';
	}
	abstract public function handle(string $message): string;
}
final class BillingHandler extends Handler
{
	public function handle(string $message): string
	{
		return str_contains($message, 'thanh toán') ? 'Billing' : $this->forward($message);
	}
}
final class TechnicalHandler extends Handler
{
	public function handle(string $message): string
	{
		return str_contains($message, 'đăng nhập') ? 'Technical' : $this->forward($message);
	}
}
$first = new BillingHandler();
$first->next(new TechnicalHandler());
echo $first->handle('Không đăng nhập được') . PHP_EOL;
