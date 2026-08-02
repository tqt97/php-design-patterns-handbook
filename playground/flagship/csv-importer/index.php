<?php

declare(strict_types=1);

interface ImportStage
{
    public function process(string $id): string;
}

final class InMemoryImportStage implements ImportStage
{
    /** @var array<string, string> */
    private array $results = [];

    public function process(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }

        return $this->results[$id] ??= 'csv-importer:' . $id . ':ok';
    }
}

final readonly class CSVImporterApplication
{
    public function __construct(private ImportStage $port) {}

    public function run(string $id): string
    {
        return $this->port->process($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $app = new CSVImporterApplication(new InMemoryImportStage());
    echo $app->run('demo'), PHP_EOL;
}
