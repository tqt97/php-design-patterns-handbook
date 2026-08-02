<?php

declare(strict_types=1);

final class CustomerService
{
	public function __construct(private PDO $pdo)
	{
	}
	public function activate(int $id): void
	{
		$this->pdo->exec("UPDATE customers SET active = 1 WHERE id = {$id}");
	}
}
// Business logic bị gắn với SQL và PDO.
