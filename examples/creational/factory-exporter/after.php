<?php

declare(strict_types=1);

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
final class CsvExporter implements Exporter
{
	public function export(array $rows): string
	{
		return implode(',', array_keys($rows[0])) . "\n" . implode(',', array_values($rows[0]));
	}
}
final class ExporterFactory
{
	public function create(string $format): Exporter
	{
		return match ($format) { 'json' => new JsonExporter(), 'csv' => new CsvExporter(), default => throw new InvalidArgumentException('Unsupported format')};
	}
}
echo (new ExporterFactory())->create('json')->export([['name' => 'Tuan']]) . PHP_EOL;
