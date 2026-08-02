<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface OrderOperation
{
    public function run(string $input): string;
}
final class CoreOrderOperation implements OrderOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardOrderDecorator implements OrderOperation
{
    public function __construct(private OrderOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedOrderDecorator implements OrderOperation
{
    public function __construct(private OrderOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'order:' . $this->next->run($input);
    }
}
$op = new TaggedOrderDecorator(new TrimGuardOrderDecorator(new CoreOrderOperation()));
expect($op->run(' DEMO ') === 'order:demo', 'decorator order');
echo 'PASS kata 4' . PHP_EOL;
