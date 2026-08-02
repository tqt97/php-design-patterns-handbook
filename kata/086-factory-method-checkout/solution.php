<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CheckoutConnector
{
    public function connect(): string;
}
final class PrimaryCheckoutConnector implements CheckoutConnector
{
    public function connect(): string
    {
        return 'primary:checkout';
    }
}
final class BackupCheckoutConnector implements CheckoutConnector
{
    public function connect(): string
    {
        return 'backup:checkout';
    }
}
abstract class CheckoutConnectorFactory
{
    abstract protected function create(): CheckoutConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryCheckoutFactory extends CheckoutConnectorFactory
{
    protected function create(): CheckoutConnector
    {
        return new PrimaryCheckoutConnector();
    }
}
final class BackupCheckoutFactory extends CheckoutConnectorFactory
{
    protected function create(): CheckoutConnector
    {
        return new BackupCheckoutConnector();
    }
}
expect((new PrimaryCheckoutFactory())->open() === 'primary:checkout', 'primary factory');
expect((new BackupCheckoutFactory())->open() === 'backup:checkout', 'backup factory');
echo 'PASS kata 86' . PHP_EOL;
