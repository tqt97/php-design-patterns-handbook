<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface PaymentConnector
{
    public function connect(): string;
}
final class PrimaryPaymentConnector implements PaymentConnector
{
    public function connect(): string
    {
        return 'primary:payment';
    }
}
final class BackupPaymentConnector implements PaymentConnector
{
    public function connect(): string
    {
        return 'backup:payment';
    }
}
abstract class PaymentConnectorFactory
{
    abstract protected function create(): PaymentConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryPaymentFactory extends PaymentConnectorFactory
{
    protected function create(): PaymentConnector
    {
        return new PrimaryPaymentConnector();
    }
}
final class BackupPaymentFactory extends PaymentConnectorFactory
{
    protected function create(): PaymentConnector
    {
        return new BackupPaymentConnector();
    }
}
expect((new PrimaryPaymentFactory())->open() === 'primary:payment', 'primary factory');
expect((new BackupPaymentFactory())->open() === 'backup:payment', 'backup factory');
echo 'PASS kata 62' . PHP_EOL;
