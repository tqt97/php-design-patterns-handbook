<?php

declare(strict_types=1);

interface Sender
{
	public function send(string $recipient, string $message): bool;
}
final class FakeSmsSender implements Sender
{
	public function send(string $recipient, string $message): bool
	{
		return true;
	}
}
final class LoggingSender implements Sender
{
	public function __construct(private Sender $next)
	{
	}
	public function send(string $recipient, string $message): bool
	{
		$result = $this->next->send($recipient, $message);
		echo "sent=" . ($result ? 'yes' : 'no') . "\n";
		return $result;
	}
}
final class RetrySender implements Sender
{
	public function __construct(private Sender $next, private int $attempts = 3)
	{
	}
	public function send(string $recipient, string $message): bool
	{
		for ($i = 0; $i < $this->attempts; $i++) {
			if ($this->next->send($recipient, $message))
				return true;
		}
		return false;
	}
}
