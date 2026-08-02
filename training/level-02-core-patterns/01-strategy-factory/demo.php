<?php

declare(strict_types=1);

interface StrategyvFactoryDemo
{
    public function run(string $input): string;
}

final class DefaultStrategyvFactoryDemo implements StrategyvFactoryDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '01-strategy-factory:' . $input . ':ok';
    }
}

$result = (new DefaultStrategyvFactoryDemo())->run('training');
assert($result === '01-strategy-factory:training:ok');
echo $result, PHP_EOL;
