<?php

declare(strict_types=1);

interface TransactionalOutboxPort
{
    public function execute(string $id): string;
}

final class InMemoryTransactionalOutboxPort implements TransactionalOutboxPort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '05-transactional-outbox:' . $id . ':ok';
    }
}

final readonly class TransactionalOutbox
{
    public function __construct(private TransactionalOutboxPort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new TransactionalOutbox(new InMemoryTransactionalOutboxPort()))->execute('demo'), PHP_EOL;
}
