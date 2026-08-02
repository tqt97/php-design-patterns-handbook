<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface PaymentOperation
{
    public function run(string $input): string;
}
final class CorePaymentOperation implements PaymentOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardPaymentDecorator implements PaymentOperation
{
    public function __construct(private PaymentOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedPaymentDecorator implements PaymentOperation
{
    public function __construct(private PaymentOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'payment:' . $this->next->run($input);
    }
}
$op = new TaggedPaymentDecorator(new TrimGuardPaymentDecorator(new CorePaymentOperation()));
expect($op->run(' DEMO ') === 'payment:demo', 'decorator order');
echo 'PASS kata 28' . PHP_EOL;
