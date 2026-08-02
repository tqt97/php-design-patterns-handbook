<?php

declare(strict_types=1);

interface NotificationChannel
{
	public function send(string $recipient, string $message): string;
}
final class EmailChannel implements NotificationChannel
{
	public function send(string $recipient, string $message): string
	{
		return "Email to {$recipient}: {$message}";
	}
}
final class SmsChannel implements NotificationChannel
{
	public function send(string $recipient, string $message): string
	{
		return "SMS to {$recipient}: {$message}";
	}
}
final class NotificationService
{
	public function __construct(private NotificationChannel $channel)
	{
	}
	public function send(string $recipient, string $message): string
	{
		return $this->channel->send($recipient, $message);
	}
}
