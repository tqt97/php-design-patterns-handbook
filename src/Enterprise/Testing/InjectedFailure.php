<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Testing;

use RuntimeException;

final class InjectedFailure extends RuntimeException
{
    public function __construct(
        public readonly string $checkpoint,
        public readonly int $call,
        public readonly int $seed,
    ) {
        parent::__construct("Injected failure at {$checkpoint}, call {$call}, seed {$seed}.");
    }
}
