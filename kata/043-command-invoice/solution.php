<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InvoiceCommand
{
    public function execute(): string;
}
final class CreateInvoiceCommand implements InvoiceCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class InvoiceCommandBus
{
    public array $audit = [];
    public function dispatch(InvoiceCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new InvoiceCommandBus();
expect($bus->dispatch(new CreateInvoiceCommand('INV-2026-001')) === 'created:INV-2026-001', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 43' . PHP_EOL;
