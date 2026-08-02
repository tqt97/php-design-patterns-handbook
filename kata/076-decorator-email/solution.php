<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface EmailOperation
{
    public function run(string $input): string;
}
final class CoreEmailOperation implements EmailOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardEmailDecorator implements EmailOperation
{
    public function __construct(private EmailOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedEmailDecorator implements EmailOperation
{
    public function __construct(private EmailOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'email:' . $this->next->run($input);
    }
}
$op = new TaggedEmailDecorator(new TrimGuardEmailDecorator(new CoreEmailOperation()));
expect($op->run(' DEMO ') === 'email:demo', 'decorator order');
echo 'PASS kata 76' . PHP_EOL;
