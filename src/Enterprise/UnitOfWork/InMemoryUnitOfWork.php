<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\UnitOfWork;

final class InMemoryUnitOfWork implements UnitOfWork
{
    private bool $running = false;

    public function transactional(callable $operation): mixed
    {
        if ($this->running) {
            throw new \LogicException('Nested transactions are not supported by this teaching implementation.');
        }

        $this->running = true;

        try {
            return $operation();
        } finally {
            $this->running = false;
        }
    }
}
