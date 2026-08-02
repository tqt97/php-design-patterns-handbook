<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface UserOperation
{
    public function run(string $input): string;
}
final class CoreUserOperation implements UserOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardUserDecorator implements UserOperation
{
    public function __construct(private UserOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedUserDecorator implements UserOperation
{
    public function __construct(private UserOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'user:' . $this->next->run($input);
    }
}
$op = new TaggedUserDecorator(new TrimGuardUserDecorator(new CoreUserOperation()));
expect($op->run(' DEMO ') === 'user:demo', 'decorator order');
echo 'PASS kata 184' . PHP_EOL;
