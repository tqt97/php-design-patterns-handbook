<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Resilience\RateLimiter;

use DateTimeImmutable;
use InvalidArgumentException;

final class FixedWindowRateLimiter
{
    /** @var array<string, array{window:int,count:int}> */
    private array $buckets = [];

    public function __construct(
        private readonly int $limit,
        private readonly int $windowSeconds,
    ) {
        if ($limit < 1 || $windowSeconds < 1) {
            throw new InvalidArgumentException('Limit and window must be positive integers.');
        }
    }

    public function acquire(string $key, DateTimeImmutable $now): RateLimitDecision
    {
        if ($key === '') {
            throw new InvalidArgumentException('Rate-limit key cannot be empty.');
        }

        $timestamp = $now->getTimestamp();
        $window = intdiv($timestamp, $this->windowSeconds);
        $bucket = $this->buckets[$key] ?? ['window' => $window, 'count' => 0];

        if ($bucket['window'] !== $window) {
            $bucket = ['window' => $window, 'count' => 0];
        }

        if ($bucket['count'] >= $this->limit) {
            $nextWindow = ($window + 1) * $this->windowSeconds;

            return new RateLimitDecision(false, 0, max(1, $nextWindow - $timestamp));
        }

        $bucket['count']++;
        $this->buckets[$key] = $bucket;

        return new RateLimitDecision(true, $this->limit - $bucket['count'], 0);
    }
}
