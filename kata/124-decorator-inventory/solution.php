<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InventoryOperation
{
    public function run(string $input): string;
}
final class CoreInventoryOperation implements InventoryOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardInventoryDecorator implements InventoryOperation
{
    public function __construct(private InventoryOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedInventoryDecorator implements InventoryOperation
{
    public function __construct(private InventoryOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'inventory:' . $this->next->run($input);
    }
}
$op = new TaggedInventoryDecorator(new TrimGuardInventoryDecorator(new CoreInventoryOperation()));
expect($op->run(' DEMO ') === 'inventory:demo', 'decorator order');
echo 'PASS kata 124' . PHP_EOL;
