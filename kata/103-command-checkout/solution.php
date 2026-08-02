<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CheckoutCommand
{
    public function execute(): string;
}
final class CreateCheckoutCommand implements CheckoutCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class CheckoutCommandBus
{
    public array $audit = [];
    public function dispatch(CheckoutCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new CheckoutCommandBus();
expect($bus->dispatch(new CreateCheckoutCommand('checkout-101')) === 'created:checkout-101', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 103' . PHP_EOL;
