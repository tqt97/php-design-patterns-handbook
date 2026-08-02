<?php

declare(strict_types=1);

namespace DesignPatterns\Infrastructure\Outbox;

use InvalidArgumentException;
use OutOfBoundsException;

final class InMemoryOutboxRepository implements OutboxRepository
{
    /** @var array<string, OutboxMessage> */
    private array $pending = [];

    public function add(OutboxMessage $message): void
    {
        $this->pending[$message->id] ??= $message;
    }

    public function pending(int $limit): array
    {
        if ($limit < 1) {
            throw new InvalidArgumentException('Pending message limit must be positive.');
        }

        return array_slice(array_values($this->pending), 0, $limit);
    }

    public function markPublished(string $id): void
    {
        if (! isset($this->pending[$id])) {
            throw new OutOfBoundsException("Outbox message {$id} was not found.");
        }

        unset($this->pending[$id]);
    }
}
