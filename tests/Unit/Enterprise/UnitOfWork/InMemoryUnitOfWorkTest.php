<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\UnitOfWork;

use DesignPatterns\Enterprise\UnitOfWork\InMemoryUnitOfWork;
use LogicException;
use PHPUnit\Framework\TestCase;

final class InMemoryUnitOfWorkTest extends TestCase
{
    public function testItReturnsTheOperationResult(): void
    {
        self::assertSame('ok', (new InMemoryUnitOfWork())->transactional(static fn (): string => 'ok'));
    }

    public function testItRejectsNestedTransactionsInTheTeachingImplementation(): void
    {
        $unitOfWork = new InMemoryUnitOfWork();

        $this->expectException(LogicException::class);
        $unitOfWork->transactional(fn () => $unitOfWork->transactional(static fn (): null => null));
    }
}
