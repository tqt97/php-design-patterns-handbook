<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InvoiceOperation
{
    public function run(string $input): string;
}
final class CoreInvoiceOperation implements InvoiceOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardInvoiceDecorator implements InvoiceOperation
{
    public function __construct(private InvoiceOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedInvoiceDecorator implements InvoiceOperation
{
    public function __construct(private InvoiceOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'invoice:' . $this->next->run($input);
    }
}
$op = new TaggedInvoiceDecorator(new TrimGuardInvoiceDecorator(new CoreInvoiceOperation()));
expect($op->run(' DEMO ') === 'invoice:demo', 'decorator order');
echo 'PASS kata 196' . PHP_EOL;
