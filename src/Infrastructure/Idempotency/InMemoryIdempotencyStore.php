<?php

declare(strict_types=1);

namespace DesignPatterns\Infrastructure\Idempotency;

use DomainException;

final class InMemoryIdempotencyStore implements IdempotencyStore
{
    /** @var array<string, IdempotencyRecord> */
    private array $records = [];

    public function find(string $key): ?IdempotencyRecord
    {
        return $this->records[$key] ?? null;
    }

    public function save(IdempotencyRecord $record): void
    {
        $existing = $this->records[$record->key] ?? null;

        if ($existing !== null && ! $existing->matchesPayload($record->payloadHash)) {
            throw new DomainException('An idempotency key cannot be reused with a different payload.');
        }

        $this->records[$record->key] = $existing ?? $record;
    }
}
