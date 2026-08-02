<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface AuditCommand
{
    public function execute(): string;
}
final class CreateAuditCommand implements AuditCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class AuditCommandBus
{
    public array $audit = [];
    public function dispatch(AuditCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new AuditCommandBus();
expect($bus->dispatch(new CreateAuditCommand('user.updated')) === 'created:user.updated', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 91' . PHP_EOL;
