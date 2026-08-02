<?php

declare(strict_types=1);

namespace DesignPatterns\Domain;

interface DomainEvent
{
    public function occurredAt(): \DateTimeImmutable;
}
