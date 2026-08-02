<?php

declare(strict_types=1);

namespace Tests\Unit\Creational\Factory;

use DesignPatterns\Creational\Factory\ExporterFactory;
use DesignPatterns\Creational\Factory\JsonExporter;
use PHPUnit\Framework\TestCase;

final class ExporterFactoryTest extends TestCase
{
    public function test_it_creates_json_exporter(): void
    {
        self::assertInstanceOf(JsonExporter::class, (new ExporterFactory())->create('json'));
    }
}
