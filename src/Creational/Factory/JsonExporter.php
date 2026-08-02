<?php

declare(strict_types=1);

namespace DesignPatterns\Creational\Factory;

use JsonException;

final class JsonExporter implements Exporter
{
    /** @throws JsonException */
    public function export(array $rows): string
    {
        return json_encode($rows, JSON_THROW_ON_ERROR);
    }
}
