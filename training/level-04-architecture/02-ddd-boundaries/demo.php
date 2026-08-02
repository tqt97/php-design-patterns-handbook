<?php

declare(strict_types=1);

interface DDDBoundaryDemo
{
    public function run(string $input): string;
}

final class DefaultDDDBoundaryDemo implements DDDBoundaryDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '02-ddd-boundaries:' . $input . ':ok';
    }
}

$result = (new DefaultDDDBoundaryDemo())->run('training');
assert($result === '02-ddd-boundaries:training:ok');
echo $result, PHP_EOL;
