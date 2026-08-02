<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SearchConnector
{
    public function connect(): string;
}
final class PrimarySearchConnector implements SearchConnector
{
    public function connect(): string
    {
        return 'primary:search';
    }
}
final class BackupSearchConnector implements SearchConnector
{
    public function connect(): string
    {
        return 'backup:search';
    }
}
abstract class SearchConnectorFactory
{
    abstract protected function create(): SearchConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimarySearchFactory extends SearchConnectorFactory
{
    protected function create(): SearchConnector
    {
        return new PrimarySearchConnector();
    }
}
final class BackupSearchFactory extends SearchConnectorFactory
{
    protected function create(): SearchConnector
    {
        return new BackupSearchConnector();
    }
}
expect((new PrimarySearchFactory())->open() === 'primary:search', 'primary factory');
expect((new BackupSearchFactory())->open() === 'backup:search', 'backup factory');
echo 'PASS kata 122' . PHP_EOL;
