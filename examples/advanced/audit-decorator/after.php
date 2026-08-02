<?php

declare(strict_types=1);

interface Action
{
    public function execute(string $id): string;
}

final class InMemoryAction implements Action
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'audit:' . $id . ':ok';
    }
}

final readonly class AuditDecoratorUseCase
{
    public function __construct(private Action $component)
    {
    }

    public function handle(string $id): string
    {
        return $this->component->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new AuditDecoratorUseCase(new InMemoryAction()))->handle('demo-1'), PHP_EOL;
}
