<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Idempotency;

use DesignPatterns\Infrastructure\Idempotency\IdempotencyRecord;
use DesignPatterns\Infrastructure\Idempotency\InMemoryIdempotencyStore;
use DomainException;
use PHPUnit\Framework\TestCase;

final class InMemoryIdempotencyStoreTest extends TestCase
{
    public function testItReplaysTheFirstResultForTheSamePayload(): void
    {
        $store = new InMemoryIdempotencyStore();
        $hash = hash('sha256', 'payload');
        $store->save(new IdempotencyRecord('key-1', $hash, 'accepted'));
        $store->save(new IdempotencyRecord('key-1', $hash, 'ignored'));

        self::assertSame('accepted', $store->find('key-1')?->response);
    }

    public function testItRejectsKeyReuseWithDifferentPayload(): void
    {
        $store = new InMemoryIdempotencyStore();
        $store->save(new IdempotencyRecord('key-1', hash('sha256', 'a'), 'accepted'));

        $this->expectException(DomainException::class);
        $store->save(new IdempotencyRecord('key-1', hash('sha256', 'b'), 'accepted'));
    }
}
