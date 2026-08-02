<?php

declare(strict_types=1);

interface MessageFormatter
{
	public function format(string $text): string;
}
final class PlainFormatter implements MessageFormatter
{
	public function format(string $text): string
	{
		return $text;
	}
}
final class TimestampFormatter implements MessageFormatter
{
	public function __construct(private MessageFormatter $next)
	{
	}
	public function format(string $text): string
	{
		return '[2026-08-01] ' . $this->next->format($text);
	}
}
final class EmailMaskFormatter implements MessageFormatter
{
	public function __construct(private MessageFormatter $next)
	{
	}
	public function format(string $text): string
	{
		return preg_replace('/([a-z])[a-z0-9._-]*(@)/i', '$1***$2', $this->next->format($text)) ?? $text;
	}
}
