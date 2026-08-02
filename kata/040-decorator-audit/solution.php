<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface AuditOperation
{
    public function run(string $input): string;
}
final class CoreAuditOperation implements AuditOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardAuditDecorator implements AuditOperation
{
    public function __construct(private AuditOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedAuditDecorator implements AuditOperation
{
    public function __construct(private AuditOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'audit:' . $this->next->run($input);
    }
}
$op = new TaggedAuditDecorator(new TrimGuardAuditDecorator(new CoreAuditOperation()));
expect($op->run(' DEMO ') === 'audit:demo', 'decorator order');
echo 'PASS kata 40' . PHP_EOL;
