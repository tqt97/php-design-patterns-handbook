<?php

declare(strict_types=1);
require dirname(__DIR__) . '/Benchmark.php';

interface Exporter
{
    public function export(array $rows): string;
}
final class JsonExporter implements Exporter
{
    public function export(array $rows): string
    {
        return json_encode($rows, JSON_THROW_ON_ERROR);
    }
}
final class ExporterFactory
{
    public function create(string $type): Exporter
    {
        return match ($type) { 'json' => new JsonExporter(), default => throw new InvalidArgumentException($type)};
    }
}
$factory = new ExporterFactory();
$rows = [['id' => 1]];

$results = [
    'direct new' => Benchmark::measure(fn(): string => (new JsonExporter())->export($rows), 50_000),
    'factory create' => Benchmark::measure(fn(): string => $factory->create('json')->export($rows), 50_000),
];
Benchmark::report('Factory vs Direct New', $results);
