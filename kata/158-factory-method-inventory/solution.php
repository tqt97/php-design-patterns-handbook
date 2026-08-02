<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InventoryConnector
{
    public function connect(): string;
}
final class PrimaryInventoryConnector implements InventoryConnector
{
    public function connect(): string
    {
        return 'primary:inventory';
    }
}
final class BackupInventoryConnector implements InventoryConnector
{
    public function connect(): string
    {
        return 'backup:inventory';
    }
}
abstract class InventoryConnectorFactory
{
    abstract protected function create(): InventoryConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryInventoryFactory extends InventoryConnectorFactory
{
    protected function create(): InventoryConnector
    {
        return new PrimaryInventoryConnector();
    }
}
final class BackupInventoryFactory extends InventoryConnectorFactory
{
    protected function create(): InventoryConnector
    {
        return new BackupInventoryConnector();
    }
}
expect((new PrimaryInventoryFactory())->open() === 'primary:inventory', 'primary factory');
expect((new BackupInventoryFactory())->open() === 'backup:inventory', 'backup factory');
echo 'PASS kata 158' . PHP_EOL;
