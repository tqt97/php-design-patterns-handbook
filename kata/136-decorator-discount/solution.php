<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface DiscountOperation
{
    public function run(string $input): string;
}
final class CoreDiscountOperation implements DiscountOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardDiscountDecorator implements DiscountOperation
{
    public function __construct(private DiscountOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedDiscountDecorator implements DiscountOperation
{
    public function __construct(private DiscountOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'discount:' . $this->next->run($input);
    }
}
$op = new TaggedDiscountDecorator(new TrimGuardDiscountDecorator(new CoreDiscountOperation()));
expect($op->run(' DEMO ') === 'discount:demo', 'decorator order');
echo 'PASS kata 136' . PHP_EOL;
