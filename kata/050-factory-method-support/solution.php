<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SupportConnector
{
    public function connect(): string;
}
final class PrimarySupportConnector implements SupportConnector
{
    public function connect(): string
    {
        return 'primary:support';
    }
}
final class BackupSupportConnector implements SupportConnector
{
    public function connect(): string
    {
        return 'backup:support';
    }
}
abstract class SupportConnectorFactory
{
    abstract protected function create(): SupportConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimarySupportFactory extends SupportConnectorFactory
{
    protected function create(): SupportConnector
    {
        return new PrimarySupportConnector();
    }
}
final class BackupSupportFactory extends SupportConnectorFactory
{
    protected function create(): SupportConnector
    {
        return new BackupSupportConnector();
    }
}
expect((new PrimarySupportFactory())->open() === 'primary:support', 'primary factory');
expect((new BackupSupportFactory())->open() === 'backup:support', 'backup factory');
echo 'PASS kata 50' . PHP_EOL;
