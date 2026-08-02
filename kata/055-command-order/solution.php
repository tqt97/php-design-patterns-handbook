<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface OrderCommand
{
    public function execute(): string;
}
final class CreateOrderCommand implements OrderCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class OrderCommandBus
{
    public array $audit = [];
    public function dispatch(OrderCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new OrderCommandBus();
expect($bus->dispatch(new CreateOrderCommand('ORD-1001')) === 'created:ORD-1001', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 55' . PHP_EOL;
