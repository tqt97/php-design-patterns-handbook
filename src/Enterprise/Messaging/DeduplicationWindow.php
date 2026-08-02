<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Messaging;

use DateTimeImmutable;
use InvalidArgumentException;

final class DeduplicationWindow
{
    /** @var array<string, DateTimeImmutable> */
    private array $seen = [];

    public function __construct(private readonly int $ttlSeconds)
    {
        if ($ttlSeconds < 1) {
            throw new InvalidArgumentException('TTL must be greater than zero.');
        }
    }

    public function firstSeen(string $messageId, DateTimeImmutable $now): bool
    {
        $this->evictExpired($now);

        if (isset($this->seen[$messageId])) {
            return false;
        }

        $this->seen[$messageId] = $now->modify(sprintf('+%d seconds', $this->ttlSeconds));

        return true;
    }

    public function contains(string $messageId, DateTimeImmutable $now): bool
    {
        $this->evictExpired($now);
        return isset($this->seen[$messageId]);
    }

    private function evictExpired(DateTimeImmutable $now): void
    {
        foreach ($this->seen as $messageId => $expiresAt) {
            if ($expiresAt <= $now) {
                unset($this->seen[$messageId]);
            }
        }
    }
}
