<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CrmConnector
{
    public function connect(): string;
}
final class PrimaryCrmConnector implements CrmConnector
{
    public function connect(): string
    {
        return 'primary:crm';
    }
}
final class BackupCrmConnector implements CrmConnector
{
    public function connect(): string
    {
        return 'backup:crm';
    }
}
abstract class CrmConnectorFactory
{
    abstract protected function create(): CrmConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryCrmFactory extends CrmConnectorFactory
{
    protected function create(): CrmConnector
    {
        return new PrimaryCrmConnector();
    }
}
final class BackupCrmFactory extends CrmConnectorFactory
{
    protected function create(): CrmConnector
    {
        return new BackupCrmConnector();
    }
}
expect((new PrimaryCrmFactory())->open() === 'primary:crm', 'primary factory');
expect((new BackupCrmFactory())->open() === 'backup:crm', 'backup factory');
echo 'PASS kata 2' . PHP_EOL;
