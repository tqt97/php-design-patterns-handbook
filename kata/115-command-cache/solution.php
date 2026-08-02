<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CacheCommand
{
    public function execute(): string;
}
final class CreateCacheCommand implements CacheCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class CacheCommandBus
{
    public array $audit = [];
    public function dispatch(CacheCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new CacheCommandBus();
expect($bus->dispatch(new CreateCacheCommand('customer:42')) === 'created:customer:42', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 115' . PHP_EOL;
