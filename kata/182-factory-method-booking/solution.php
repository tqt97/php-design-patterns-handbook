<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface BookingConnector
{
    public function connect(): string;
}
final class PrimaryBookingConnector implements BookingConnector
{
    public function connect(): string
    {
        return 'primary:booking';
    }
}
final class BackupBookingConnector implements BookingConnector
{
    public function connect(): string
    {
        return 'backup:booking';
    }
}
abstract class BookingConnectorFactory
{
    abstract protected function create(): BookingConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryBookingFactory extends BookingConnectorFactory
{
    protected function create(): BookingConnector
    {
        return new PrimaryBookingConnector();
    }
}
final class BackupBookingFactory extends BookingConnectorFactory
{
    protected function create(): BookingConnector
    {
        return new BackupBookingConnector();
    }
}
expect((new PrimaryBookingFactory())->open() === 'primary:booking', 'primary factory');
expect((new BackupBookingFactory())->open() === 'backup:booking', 'backup factory');
echo 'PASS kata 182' . PHP_EOL;
