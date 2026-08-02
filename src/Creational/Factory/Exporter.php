<?php

declare(strict_types=1);

namespace DesignPatterns\Creational\Factory;

interface Exporter
{
    /** @param list<array<string, scalar|null>> $rows */
    public function export(array $rows): string;
}
