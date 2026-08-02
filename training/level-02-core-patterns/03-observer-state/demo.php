<?php

declare(strict_types=1);

interface ObservervStateDemo
{
    public function run(string $input): string;
}

final class DefaultObservervStateDemo implements ObservervStateDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '03-observer-state:' . $input . ':ok';
    }
}

$result = (new DefaultObservervStateDemo())->run('training');
assert($result === '03-observer-state:training:ok');
echo $result, PHP_EOL;
