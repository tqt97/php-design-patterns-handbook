<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SupportCommand
{
    public function execute(): string;
}
final class CreateSupportCommand implements SupportCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class SupportCommandBus
{
    public array $audit = [];
    public function dispatch(SupportCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new SupportCommandBus();
expect($bus->dispatch(new CreateSupportCommand('TICKET-88')) === 'created:TICKET-88', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 67' . PHP_EOL;
