<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InvoiceConnector
{
    public function connect(): string;
}
final class PrimaryInvoiceConnector implements InvoiceConnector
{
    public function connect(): string
    {
        return 'primary:invoice';
    }
}
final class BackupInvoiceConnector implements InvoiceConnector
{
    public function connect(): string
    {
        return 'backup:invoice';
    }
}
abstract class InvoiceConnectorFactory
{
    abstract protected function create(): InvoiceConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryInvoiceFactory extends InvoiceConnectorFactory
{
    protected function create(): InvoiceConnector
    {
        return new PrimaryInvoiceConnector();
    }
}
final class BackupInvoiceFactory extends InvoiceConnectorFactory
{
    protected function create(): InvoiceConnector
    {
        return new BackupInvoiceConnector();
    }
}
expect((new PrimaryInvoiceFactory())->open() === 'primary:invoice', 'primary factory');
expect((new BackupInvoiceFactory())->open() === 'backup:invoice', 'backup factory');
echo 'PASS kata 26' . PHP_EOL;
