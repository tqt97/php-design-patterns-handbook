<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface EmailConnector
{
    public function connect(): string;
}
final class PrimaryEmailConnector implements EmailConnector
{
    public function connect(): string
    {
        return 'primary:email';
    }
}
final class BackupEmailConnector implements EmailConnector
{
    public function connect(): string
    {
        return 'backup:email';
    }
}
abstract class EmailConnectorFactory
{
    abstract protected function create(): EmailConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryEmailFactory extends EmailConnectorFactory
{
    protected function create(): EmailConnector
    {
        return new PrimaryEmailConnector();
    }
}
final class BackupEmailFactory extends EmailConnectorFactory
{
    protected function create(): EmailConnector
    {
        return new BackupEmailConnector();
    }
}
expect((new PrimaryEmailFactory())->open() === 'primary:email', 'primary factory');
expect((new BackupEmailFactory())->open() === 'backup:email', 'backup factory');
echo 'PASS kata 110' . PHP_EOL;
