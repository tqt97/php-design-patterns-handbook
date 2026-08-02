<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CacheOperation
{
    public function run(string $input): string;
}
final class CoreCacheOperation implements CacheOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardCacheDecorator implements CacheOperation
{
    public function __construct(private CacheOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedCacheDecorator implements CacheOperation
{
    public function __construct(private CacheOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'cache:' . $this->next->run($input);
    }
}
$op = new TaggedCacheDecorator(new TrimGuardCacheDecorator(new CoreCacheOperation()));
expect($op->run(' DEMO ') === 'cache:demo', 'decorator order');
echo 'PASS kata 64' . PHP_EOL;
