<?php

declare(strict_types=1);

namespace DesignPatterns\Infrastructure\Clock;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
