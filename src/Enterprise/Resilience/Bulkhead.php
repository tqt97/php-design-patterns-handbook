<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience;

use InvalidArgumentException;

/**
 * A small synchronous bulkhead used to demonstrate admission control semantics.
 *
 * Production implementations usually coordinate permits across processes and
 * export queue/rejection metrics. This class intentionally models only the
 * invariant: active executions must never exceed the configured capacity.
 */
final class Bulkhead
{
    private int $active = 0;

    public function __construct(private readonly int $capacity)
    {
        if ($capacity < 1) {
            throw new InvalidArgumentException('Bulkhead capacity must be greater than zero.');
        }
    }

    public function active(): int
    {
        return $this->active;
    }

    public function available(): int
    {
        return $this->capacity - $this->active;
    }

    /** @template T @param callable(): T $operation @return T */
    public function execute(callable $operation): mixed
    {
        if ($this->active >= $this->capacity) {
            throw new BulkheadRejectedException('Bulkhead capacity is exhausted.');
        }

        ++$this->active;

        try {
            return $operation();
        } finally {
            --$this->active;
        }
    }
}
