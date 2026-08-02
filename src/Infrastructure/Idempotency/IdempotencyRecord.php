<?php

declare(strict_types=1);

namespace DesignPatterns\Infrastructure\Idempotency;

use InvalidArgumentException;

final readonly class IdempotencyRecord
{
    public function __construct(
        public string $key,
        public string $payloadHash,
        public string $response,
    ) {
        if (trim($key) === '') {
            throw new InvalidArgumentException('Idempotency key is required.');
        }

        if (preg_match('/^[a-f0-9]{64}$/', $payloadHash) !== 1) {
            throw new InvalidArgumentException('Payload hash must be a SHA-256 hexadecimal string.');
        }
    }

    public function matchesPayload(string $payloadHash): bool
    {
        return hash_equals($this->payloadHash, $payloadHash);
    }
}
