<?php

declare(strict_types=1);

abstract class Rule
{
	private ?Rule $next = null;
	public function next(Rule $rule): Rule
	{
		$this->next = $rule;
		return $rule;
	}
	protected function forward(array $data): ?string
	{
		return $this->next?->validate($data);
	}
	abstract public function validate(array $data): ?string;
}
final class EmailRequired extends Rule
{
	public function validate(array $data): ?string
	{
		return empty($data['email']) ? 'email required' : $this->forward($data);
	}
}
final class StrongPassword extends Rule
{
	public function validate(array $data): ?string
	{
		return strlen($data['password'] ?? '') < 8 ? 'password short' : $this->forward($data);
	}
}
