<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ShippingConnector
{
    public function connect(): string;
}
final class PrimaryShippingConnector implements ShippingConnector
{
    public function connect(): string
    {
        return 'primary:shipping';
    }
}
final class BackupShippingConnector implements ShippingConnector
{
    public function connect(): string
    {
        return 'backup:shipping';
    }
}
abstract class ShippingConnectorFactory
{
    abstract protected function create(): ShippingConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryShippingFactory extends ShippingConnectorFactory
{
    protected function create(): ShippingConnector
    {
        return new PrimaryShippingConnector();
    }
}
final class BackupShippingFactory extends ShippingConnectorFactory
{
    protected function create(): ShippingConnector
    {
        return new BackupShippingConnector();
    }
}
expect((new PrimaryShippingFactory())->open() === 'primary:shipping', 'primary factory');
expect((new BackupShippingFactory())->open() === 'backup:shipping', 'backup factory');
echo 'PASS kata 146' . PHP_EOL;
