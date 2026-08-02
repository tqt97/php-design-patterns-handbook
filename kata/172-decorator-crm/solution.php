<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CrmOperation
{
    public function run(string $input): string;
}
final class CoreCrmOperation implements CrmOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardCrmDecorator implements CrmOperation
{
    public function __construct(private CrmOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedCrmDecorator implements CrmOperation
{
    public function __construct(private CrmOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'crm:' . $this->next->run($input);
    }
}
$op = new TaggedCrmDecorator(new TrimGuardCrmDecorator(new CoreCrmOperation()));
expect($op->run(' DEMO ') === 'crm:demo', 'decorator order');
echo 'PASS kata 172' . PHP_EOL;
