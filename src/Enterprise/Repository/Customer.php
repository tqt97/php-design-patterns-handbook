<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Repository;

final readonly class Customer
{
    public function __construct(
        public int $id,
        public string $email,
        public bool $active = true,
    ) {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Customer ID must be positive.');
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Customer email is invalid.');
        }
    }
}
