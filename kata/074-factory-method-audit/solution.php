<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface AuditConnector
{
    public function connect(): string;
}
final class PrimaryAuditConnector implements AuditConnector
{
    public function connect(): string
    {
        return 'primary:audit';
    }
}
final class BackupAuditConnector implements AuditConnector
{
    public function connect(): string
    {
        return 'backup:audit';
    }
}
abstract class AuditConnectorFactory
{
    abstract protected function create(): AuditConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryAuditFactory extends AuditConnectorFactory
{
    protected function create(): AuditConnector
    {
        return new PrimaryAuditConnector();
    }
}
final class BackupAuditFactory extends AuditConnectorFactory
{
    protected function create(): AuditConnector
    {
        return new BackupAuditConnector();
    }
}
expect((new PrimaryAuditFactory())->open() === 'primary:audit', 'primary factory');
expect((new BackupAuditFactory())->open() === 'backup:audit', 'backup factory');
echo 'PASS kata 74' . PHP_EOL;
