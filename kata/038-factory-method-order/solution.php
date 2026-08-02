<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface OrderConnector
{
    public function connect(): string;
}
final class PrimaryOrderConnector implements OrderConnector
{
    public function connect(): string
    {
        return 'primary:order';
    }
}
final class BackupOrderConnector implements OrderConnector
{
    public function connect(): string
    {
        return 'backup:order';
    }
}
abstract class OrderConnectorFactory
{
    abstract protected function create(): OrderConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryOrderFactory extends OrderConnectorFactory
{
    protected function create(): OrderConnector
    {
        return new PrimaryOrderConnector();
    }
}
final class BackupOrderFactory extends OrderConnectorFactory
{
    protected function create(): OrderConnector
    {
        return new BackupOrderConnector();
    }
}
expect((new PrimaryOrderFactory())->open() === 'primary:order', 'primary factory');
expect((new BackupOrderFactory())->open() === 'backup:order', 'backup factory');
echo 'PASS kata 38' . PHP_EOL;
