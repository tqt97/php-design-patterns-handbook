<?php

declare(strict_types=1);

interface ProductionReadinessDemo
{
    public function run(string $input): string;
}

final class DefaultProductionReadinessDemo implements ProductionReadinessDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '03-production-readiness:' . $input . ':ok';
    }
}

$result = (new DefaultProductionReadinessDemo())->run('training');
assert($result === '03-production-readiness:training:ok');
echo $result, PHP_EOL;
