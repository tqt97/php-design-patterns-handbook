<?php

declare(strict_types=1);

final class RegistrationValidator
{
	public function validate(array $data): array
	{
		$errors = [];
		if (empty($data['email']))
			$errors[] = 'email required';
		if (strlen($data['password'] ?? '') < 8)
			$errors[] = 'password short';
		return $errors;
	}
}
