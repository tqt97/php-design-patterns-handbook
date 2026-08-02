<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Outbox;

use DateTimeImmutable;
use DesignPatterns\Infrastructure\Outbox\OutboxMessage;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class OutboxMessageTest extends TestCase
{
    public function testItKeepsEnvelopeDataImmutable(): void
    {
        $time = new DateTimeImmutable('2026-08-01T10:00:00+00:00');
        $message = new OutboxMessage('evt-1', 'OrderPlaced', ['orderId' => 10], $time);

        self::assertSame('evt-1', $message->id);
        self::assertSame('OrderPlaced', $message->type);
        self::assertSame(['orderId' => 10], $message->payload);
        self::assertSame($time, $message->occurredAt);
    }

    public function testItRejectsMissingEnvelopeIdentity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new OutboxMessage('', 'OrderPlaced', [], new DateTimeImmutable());
    }
}
