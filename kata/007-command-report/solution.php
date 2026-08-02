<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ReportCommand
{
    public function execute(): string;
}
final class CreateReportCommand implements ReportCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class ReportCommandBus
{
    public array $audit = [];
    public function dispatch(ReportCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new ReportCommandBus();
expect($bus->dispatch(new CreateReportCommand('sales-monthly')) === 'created:sales-monthly', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 7' . PHP_EOL;
