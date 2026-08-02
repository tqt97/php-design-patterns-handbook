<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience\Backpressure;

use InvalidArgumentException;
use SplQueue;

/**
 * @template T
 */
final class BoundedWorkQueue
{
    /** @var SplQueue<T> */
    private SplQueue $queue;

    public function __construct(private readonly int $capacity)
    {
        if ($capacity < 1) {
            throw new InvalidArgumentException('Queue capacity must be at least one.');
        }

        $this->queue = new SplQueue();
    }

    /** @param T $work */
    public function enqueue(mixed $work): EnqueueDecision
    {
        if ($this->queue->count() >= $this->capacity) {
            return new EnqueueDecision(
                accepted: false,
                queueSize: $this->queue->count(),
                reason: 'capacity_exhausted',
            );
        }

        $this->queue->enqueue($work);

        return new EnqueueDecision(
            accepted: true,
            queueSize: $this->queue->count(),
        );
    }

    /** @return T|null */
    public function dequeue(): mixed
    {
        if ($this->queue->isEmpty()) {
            return null;
        }

        return $this->queue->dequeue();
    }

    public function size(): int
    {
        return $this->queue->count();
    }

    public function remainingCapacity(): int
    {
        return $this->capacity - $this->queue->count();
    }
}
