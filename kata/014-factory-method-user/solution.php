<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface UserConnector
{
    public function connect(): string;
}
final class PrimaryUserConnector implements UserConnector
{
    public function connect(): string
    {
        return 'primary:user';
    }
}
final class BackupUserConnector implements UserConnector
{
    public function connect(): string
    {
        return 'backup:user';
    }
}
abstract class UserConnectorFactory
{
    abstract protected function create(): UserConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryUserFactory extends UserConnectorFactory
{
    protected function create(): UserConnector
    {
        return new PrimaryUserConnector();
    }
}
final class BackupUserFactory extends UserConnectorFactory
{
    protected function create(): UserConnector
    {
        return new BackupUserConnector();
    }
}
expect((new PrimaryUserFactory())->open() === 'primary:user', 'primary factory');
expect((new BackupUserFactory())->open() === 'backup:user', 'backup factory');
echo 'PASS kata 14' . PHP_EOL;
