<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ShippingOperation
{
    public function run(string $input): string;
}
final class CoreShippingOperation implements ShippingOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardShippingDecorator implements ShippingOperation
{
    public function __construct(private ShippingOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedShippingDecorator implements ShippingOperation
{
    public function __construct(private ShippingOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'shipping:' . $this->next->run($input);
    }
}
$op = new TaggedShippingDecorator(new TrimGuardShippingDecorator(new CoreShippingOperation()));
expect($op->run(' DEMO ') === 'shipping:demo', 'decorator order');
echo 'PASS kata 112' . PHP_EOL;
