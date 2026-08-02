<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SearchOperation
{
    public function run(string $input): string;
}
final class CoreSearchOperation implements SearchOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardSearchDecorator implements SearchOperation
{
    public function __construct(private SearchOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedSearchDecorator implements SearchOperation
{
    public function __construct(private SearchOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'search:' . $this->next->run($input);
    }
}
$op = new TaggedSearchDecorator(new TrimGuardSearchDecorator(new CoreSearchOperation()));
expect($op->run(' DEMO ') === 'search:demo', 'decorator order');
echo 'PASS kata 88' . PHP_EOL;
