<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Idempotency;

use DesignPatterns\Infrastructure\Idempotency\IdempotencyRecord;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class IdempotencyRecordTest extends TestCase
{
    public function testItMatchesTheSamePayloadHashOnly(): void
    {
        $hash = hash('sha256', '{"amount":100}');
        $record = new IdempotencyRecord('pay-1', $hash, '{"id":9}');

        self::assertTrue($record->matchesPayload($hash));
        self::assertFalse($record->matchesPayload(hash('sha256', '{"amount":200}')));
    }

    public function testItRejectsInvalidHash(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new IdempotencyRecord('pay-1', 'not-a-hash', '{}');
    }
}
