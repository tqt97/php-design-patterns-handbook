<?php

declare(strict_types=1);

interface SOLIDtrongthctDemo
{
    public function run(string $input): string;
}

final class DefaultSOLIDtrongthctDemo implements SOLIDtrongthctDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '02-solid-in-practice:' . $input . ':ok';
    }
}

$result = (new DefaultSOLIDtrongthctDemo())->run('training');
assert($result === '02-solid-in-practice:training:ok');
echo $result, PHP_EOL;
