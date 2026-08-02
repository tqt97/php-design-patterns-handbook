<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CacheConnector
{
    public function connect(): string;
}
final class PrimaryCacheConnector implements CacheConnector
{
    public function connect(): string
    {
        return 'primary:cache';
    }
}
final class BackupCacheConnector implements CacheConnector
{
    public function connect(): string
    {
        return 'backup:cache';
    }
}
abstract class CacheConnectorFactory
{
    abstract protected function create(): CacheConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryCacheFactory extends CacheConnectorFactory
{
    protected function create(): CacheConnector
    {
        return new PrimaryCacheConnector();
    }
}
final class BackupCacheFactory extends CacheConnectorFactory
{
    protected function create(): CacheConnector
    {
        return new BackupCacheConnector();
    }
}
expect((new PrimaryCacheFactory())->open() === 'primary:cache', 'primary factory');
expect((new BackupCacheFactory())->open() === 'backup:cache', 'backup factory');
echo 'PASS kata 98' . PHP_EOL;
