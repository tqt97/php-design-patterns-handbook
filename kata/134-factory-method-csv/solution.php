<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CsvConnector
{
    public function connect(): string;
}
final class PrimaryCsvConnector implements CsvConnector
{
    public function connect(): string
    {
        return 'primary:csv';
    }
}
final class BackupCsvConnector implements CsvConnector
{
    public function connect(): string
    {
        return 'backup:csv';
    }
}
abstract class CsvConnectorFactory
{
    abstract protected function create(): CsvConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryCsvFactory extends CsvConnectorFactory
{
    protected function create(): CsvConnector
    {
        return new PrimaryCsvConnector();
    }
}
final class BackupCsvFactory extends CsvConnectorFactory
{
    protected function create(): CsvConnector
    {
        return new BackupCsvConnector();
    }
}
expect((new PrimaryCsvFactory())->open() === 'primary:csv', 'primary factory');
expect((new BackupCsvFactory())->open() === 'backup:csv', 'backup factory');
echo 'PASS kata 134' . PHP_EOL;
