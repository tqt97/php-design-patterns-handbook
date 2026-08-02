<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SearchCommand
{
    public function execute(): string;
}
final class CreateSearchCommand implements SearchCommand
{
    public function __construct(private string $id)
    {
    }
    public function execute(): string
    {
        return 'created:' . $this->id;
    }
}
final class SearchCommandBus
{
    public array $audit = [];
    public function dispatch(SearchCommand $command): string
    {
        $result = $command->execute();
        $this->audit[] = $result;
        return $result;
    }
}
$bus = new SearchCommandBus();
expect($bus->dispatch(new CreateSearchCommand('php-pattern')) === 'created:php-pattern', 'command result');
expect(count($bus->audit) === 1, 'audit');
echo 'PASS kata 139' . PHP_EOL;
