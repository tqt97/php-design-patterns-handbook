<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Command;

use DesignPatterns\Application\Command\Command;
use DesignPatterns\Application\Command\CommandBus;
use DesignPatterns\Application\Command\CommandHandler;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CommandBusTest extends TestCase
{
    public function test_dispatches_to_registered_handler(): void
    {
        $bus = new CommandBus();
        $handler = new class implements CommandHandler {
            public function handle(Command $command): mixed
            {
                return $command instanceof RenameCustomer ? strtoupper($command->name) : null;
            }
        };
        $bus->register(RenameCustomer::class, $handler);

        self::assertSame('TUAN', $bus->dispatch(new RenameCustomer('Tuan')));
    }

    public function test_rejects_missing_and_duplicate_handlers(): void
    {
        $bus = new CommandBus();
        $handler = new class implements CommandHandler {
            public function handle(Command $command): mixed { return null; }
        };
        $bus->register(RenameCustomer::class, $handler);

        $this->expectException(InvalidArgumentException::class);
        $bus->register(RenameCustomer::class, $handler);
    }
}

final readonly class RenameCustomer implements Command
{
    public function __construct(public string $name) {}
}
