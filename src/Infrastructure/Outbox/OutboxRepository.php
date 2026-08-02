<?php

declare(strict_types=1);

namespace DesignPatterns\Infrastructure\Outbox;

interface OutboxRepository
{
    public function add(OutboxMessage $message): void;

    /** @return list<OutboxMessage> */
    public function pending(int $limit): array;

    public function markPublished(string $id): void;
}
