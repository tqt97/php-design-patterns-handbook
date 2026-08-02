<?php

declare(strict_types=1);

interface FileExportFactoryPort
{
    public function execute(string $id): string;
}

final class InMemoryFileExportFactoryPort implements FileExportFactoryPort
{
    public function execute(string $id): string
    {
        if ($id === '') {
            throw new InvalidArgumentException('ID must not be empty.');
        }
        return '05-file-export-factory:' . $id . ':ok';
    }
}

final readonly class FileExportFactory
{
    public function __construct(private FileExportFactoryPort $port)
    {
    }
    public function execute(string $id): string
    {
        return $this->port->execute($id);
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo (new FileExportFactory(new InMemoryFileExportFactoryPort()))->execute('demo'), PHP_EOL;
}
