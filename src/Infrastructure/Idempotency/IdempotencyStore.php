<?php

declare(strict_types=1);

namespace DesignPatterns\Infrastructure\Idempotency;

interface IdempotencyStore
{
    public function find(string $key): ?IdempotencyRecord;

    public function save(IdempotencyRecord $record): void;
}
