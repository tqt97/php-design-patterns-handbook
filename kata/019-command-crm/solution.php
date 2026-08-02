<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CrmCommand
{
    public function execute(): string;
}
final class CreateCrmCommand implements CrmCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class CrmCommandBus
{
    public array $audit = [];
    public function dispatch(CrmCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new CrmCommandBus();
expect($bus->dispatch(new CreateCrmCommand('lead-202')) === 'created:lead-202', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 19' . PHP_EOL;
