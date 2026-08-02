<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Testing;

use RuntimeException;

final class FailureInjector
{
    /** @var array<string, int> */
    private array $calls = [];

    public function __construct(private readonly int $seed) {}

    public function checkpoint(string $name, int $modulo = 5): void
    {
        if ($modulo < 2) {
            throw new RuntimeException('Modulo must be at least 2.');
        }

        $call = ($this->calls[$name] ?? 0) + 1;
        $this->calls[$name] = $call;
        if ((crc32($name . ':' . $call . ':' . $this->seed) % $modulo) === 0) {
            throw new InjectedFailure($name, $call, $this->seed);
        }
    }
}
