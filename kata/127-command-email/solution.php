<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface EmailCommand
{
    public function execute(): string;
}
final class CreateEmailCommand implements EmailCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class EmailCommandBus
{
    public array $audit = [];
    public function dispatch(EmailCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new EmailCommandBus();
expect($bus->dispatch(new CreateEmailCommand('welcome@example.com')) === 'created:welcome@example.com', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 127' . PHP_EOL;
