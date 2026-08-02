<?php

declare(strict_types=1);

interface IdempotentPaymentPort
{
    public function execute(string $id): string;
}

final class InMemoryIdempotentPaymentPort implements IdempotentPaymentPort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '04-idempotent-payment:' . $id . ':ok';
    }
}

final readonly class IdempotentPayment
{
    public function __construct(private IdempotentPaymentPort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new IdempotentPayment(new InMemoryIdempotentPaymentPort()))->execute('demo'), PHP_EOL;
}
