<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface PaymentCommand
{
    public function execute(): string;
}
final class CreatePaymentCommand implements PaymentCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class PaymentCommandBus
{
    public array $audit = [];
    public function dispatch(PaymentCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new PaymentCommandBus();
expect($bus->dispatch(new CreatePaymentCommand('pay_1001')) === 'created:pay_1001', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 79' . PHP_EOL;
