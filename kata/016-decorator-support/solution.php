<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SupportOperation
{
    public function run(string $input): string;
}
final class CoreSupportOperation implements SupportOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardSupportDecorator implements SupportOperation
{
    public function __construct(private SupportOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedSupportDecorator implements SupportOperation
{
    public function __construct(private SupportOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'support:' . $this->next->run($input);
    }
}
$op = new TaggedSupportDecorator(new TrimGuardSupportDecorator(new CoreSupportOperation()));
expect($op->run(' DEMO ') === 'support:demo', 'decorator order');
echo 'PASS kata 16' . PHP_EOL;
