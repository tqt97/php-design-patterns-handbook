<?php

declare(strict_types=1);

namespace DesignPatterns\Application\Command;

use InvalidArgumentException;

final class CommandBus
{
    /** @var array<class-string<Command>, CommandHandler<Command, mixed>> */
    private array $handlers = [];

    /**
     * @template TCommand of Command
     * @template TResult
     * @param class-string<TCommand> $commandClass
     * @param CommandHandler<TCommand, TResult> $handler
     */
    public function register(string $commandClass, CommandHandler $handler): void
    {
        if (isset($this->handlers[$commandClass])) {
            throw new InvalidArgumentException("Handler already registered for {$commandClass}.");
        }

        $this->handlers[$commandClass] = $handler;
    }

    public function dispatch(Command $command): mixed
    {
        $commandClass = $command::class;
        $handler = $this->handlers[$commandClass] ?? null;

        if ($handler === null) {
            throw new InvalidArgumentException("No handler registered for {$commandClass}.");
        }

        return $handler->handle($command);
    }
}
