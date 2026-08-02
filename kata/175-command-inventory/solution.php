<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InventoryCommand
{
    public function execute(): string;
}
final class CreateInventoryCommand implements InventoryCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class InventoryCommandBus
{
    public array $audit = [];
    public function dispatch(InventoryCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new InventoryCommandBus();
expect($bus->dispatch(new CreateInventoryCommand('SKU-PHP-01')) === 'created:SKU-PHP-01', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 175' . PHP_EOL;
