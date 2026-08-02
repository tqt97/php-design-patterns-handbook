<?php

declare(strict_types=1);

namespace DesignPatterns\Creational\Factory;

use InvalidArgumentException;

final class ExporterFactory
{
    public function create(string $format): Exporter
    {
        return match ($format) {
            'json' => new JsonExporter(),
            default => throw new InvalidArgumentException("Unsupported format: {$format}"),
        };
    }
}
