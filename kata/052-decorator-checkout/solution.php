<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CheckoutOperation
{
    public function run(string $input): string;
}
final class CoreCheckoutOperation implements CheckoutOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardCheckoutDecorator implements CheckoutOperation
{
    public function __construct(private CheckoutOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedCheckoutDecorator implements CheckoutOperation
{
    public function __construct(private CheckoutOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'checkout:' . $this->next->run($input);
    }
}
$op = new TaggedCheckoutDecorator(new TrimGuardCheckoutDecorator(new CoreCheckoutOperation()));
expect($op->run(' DEMO ') === 'checkout:demo', 'decorator order');
echo 'PASS kata 52' . PHP_EOL;
