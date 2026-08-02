<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Outbox;

use DateTimeImmutable;
use DesignPatterns\Infrastructure\Outbox\InMemoryOutboxRepository;
use DesignPatterns\Infrastructure\Outbox\OutboxMessage;
use PHPUnit\Framework\TestCase;

final class InMemoryOutboxRepositoryTest extends TestCase
{
    public function testItStoresPendingMessagesAndMarksThemPublished(): void
    {
        $repository = new InMemoryOutboxRepository();
        $message = new OutboxMessage('event-1', 'order.paid', ['order_id' => 42], new DateTimeImmutable());

        $repository->add($message);
        self::assertSame([$message], $repository->pending(10));

        $repository->markPublished('event-1');
        self::assertSame([], $repository->pending(10));
    }
}
