<?php

declare(strict_types=1);

namespace Tests\Unit\Enterprise\Resilience\Backpressure;

use DesignPatterns\Enterprise\Resilience\Backpressure\BoundedWorkQueue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BoundedWorkQueueTest extends TestCase
{
    public function test_rejects_new_work_when_capacity_is_exhausted(): void
    {
        $queue = new BoundedWorkQueue(2);

        self::assertTrue($queue->enqueue('job-1')->accepted);
        self::assertTrue($queue->enqueue('job-2')->accepted);

        $rejected = $queue->enqueue('job-3');

        self::assertFalse($rejected->accepted);
        self::assertSame('capacity_exhausted', $rejected->reason);
        self::assertSame(2, $queue->size());
    }

    public function test_capacity_is_recovered_after_dequeue(): void
    {
        $queue = new BoundedWorkQueue(1);
        $queue->enqueue('job-1');

        self::assertSame('job-1', $queue->dequeue());
        self::assertTrue($queue->enqueue('job-2')->accepted);
        self::assertSame(0, $queue->remainingCapacity());
    }

    public function test_capacity_must_be_positive(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new BoundedWorkQueue(0);
    }
}
