<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface UserCommand
{
    public function execute(): string;
}
final class CreateUserCommand implements UserCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class UserCommandBus
{
    public array $audit = [];
    public function dispatch(UserCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new UserCommandBus();
expect($bus->dispatch(new CreateUserCommand('user-42')) === 'created:user-42', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 31' . PHP_EOL;
