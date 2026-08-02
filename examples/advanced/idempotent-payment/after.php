<?php

declare(strict_types=1);

interface IdempotencyStore
{
    public function charge(string $id): string;
}

final class InMemoryIdempotencyStore implements IdempotencyStore
{
    public function charge(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return 'payment:' . $id . ':ok';
    }
}

final readonly class IdempotentPaymentUseCase
{
    public function __construct(private IdempotencyStore $component) {}

    public function handle(string $id): string
    {
        return $this->component->charge($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new IdempotentPaymentUseCase(new InMemoryIdempotencyStore()))->handle('demo-1'), PHP_EOL;
}
