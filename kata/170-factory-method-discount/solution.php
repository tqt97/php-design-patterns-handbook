<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface DiscountConnector
{
    public function connect(): string;
}
final class PrimaryDiscountConnector implements DiscountConnector
{
    public function connect(): string
    {
        return 'primary:discount';
    }
}
final class BackupDiscountConnector implements DiscountConnector
{
    public function connect(): string
    {
        return 'backup:discount';
    }
}
abstract class DiscountConnectorFactory
{
    abstract protected function create(): DiscountConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryDiscountFactory extends DiscountConnectorFactory
{
    protected function create(): DiscountConnector
    {
        return new PrimaryDiscountConnector();
    }
}
final class BackupDiscountFactory extends DiscountConnectorFactory
{
    protected function create(): DiscountConnector
    {
        return new BackupDiscountConnector();
    }
}
expect((new PrimaryDiscountFactory())->open() === 'primary:discount', 'primary factory');
expect((new BackupDiscountFactory())->open() === 'backup:discount', 'backup factory');
echo 'PASS kata 170' . PHP_EOL;
