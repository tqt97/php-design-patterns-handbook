<?php

declare(strict_types=1);

interface MicroserviceConsistencyDemo
{
    public function run(string $input): string;
}

final class DefaultMicroserviceConsistencyDemo implements MicroserviceConsistencyDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '03-microservice-consistency:' . $input . ':ok';
    }
}

$result = (new DefaultMicroserviceConsistencyDemo())->run('training');
assert($result === '03-microservice-consistency:training:ok');
echo $result, PHP_EOL;
