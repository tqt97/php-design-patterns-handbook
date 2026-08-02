<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ShippingCommand
{
    public function execute(): string;
}
final class CreateShippingCommand implements ShippingCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class ShippingCommandBus
{
    public array $audit = [];
    public function dispatch(ShippingCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new ShippingCommandBus();
expect($bus->dispatch(new CreateShippingCommand('HCM-HN')) === 'created:HCM-HN', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 163' . PHP_EOL;
