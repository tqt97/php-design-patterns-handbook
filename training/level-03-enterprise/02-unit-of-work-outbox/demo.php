<?php

declare(strict_types=1);

interface TransactionvOutboxDemo
{
    public function run(string $input): string;
}

final class DefaultTransactionvOutboxDemo implements TransactionvOutboxDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '02-unit-of-work-outbox:' . $input . ':ok';
    }
}

$result = (new DefaultTransactionvOutboxDemo())->run('training');
assert($result === '02-unit-of-work-outbox:training:ok');
echo $result, PHP_EOL;
