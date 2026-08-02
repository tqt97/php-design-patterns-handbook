<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\Specification;

interface Specification
{
    public function isSatisfiedBy(object $candidate): bool;
}
