<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface DiscountCommand
{
    public function execute(): string;
}
final class CreateDiscountCommand implements DiscountCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class DiscountCommandBus
{
    public array $audit = [];
    public function dispatch(DiscountCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new DiscountCommandBus();
expect($bus->dispatch(new CreateDiscountCommand('VIP20')) === 'created:VIP20', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 187' . PHP_EOL;
