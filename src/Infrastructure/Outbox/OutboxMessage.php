<?php

declare(strict_types=1);

namespace DesignPatterns\Infrastructure\Outbox;

use InvalidArgumentException;

final readonly class OutboxMessage
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        public string $id,
        public string $type,
        public array $payload,
        public \DateTimeImmutable $occurredAt,
    ) {
        if (trim($id) === '' || trim($type) === '') {
            throw new InvalidArgumentException('Outbox message ID and type are required.');
        }
    }
}
