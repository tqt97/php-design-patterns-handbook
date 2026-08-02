<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CsvCommand
{
    public function execute(): string;
}
final class CreateCsvCommand implements CsvCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class CsvCommandBus
{
    public array $audit = [];
    public function dispatch(CsvCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new CsvCommandBus();
expect($bus->dispatch(new CreateCsvCommand('customers.csv')) === 'created:customers.csv', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 151' . PHP_EOL;
