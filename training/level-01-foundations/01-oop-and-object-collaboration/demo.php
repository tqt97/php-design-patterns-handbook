<?php

declare(strict_types=1);

interface OOPvcngtcobjectDemo
{
    public function run(string $input): string;
}

final class DefaultOOPvcngtcobjectDemo implements OOPvcngtcobjectDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '01-oop-and-object-collaboration:' . $input . ':ok';
    }
}

$result = (new DefaultOOPvcngtcobjectDemo())->run('training');
assert($result === '01-oop-and-object-collaboration:training:ok');
echo $result, PHP_EOL;
