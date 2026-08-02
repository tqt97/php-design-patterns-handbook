<?php

declare(strict_types=1);

namespace DesignPatterns\Enterprise\UnitOfWork;

interface UnitOfWork
{
    public function transactional(callable $operation): mixed;
}
