<?php

declare(strict_types=1);

interface ADRvGovernanceDemo
{
    public function run(string $input): string;
}

final class DefaultADRvGovernanceDemo implements ADRvGovernanceDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '02-adr-and-governance:' . $input . ':ok';
    }
}

$result = (new DefaultADRvGovernanceDemo())->run('training');
assert($result === '02-adr-and-governance:training:ok');
echo $result, PHP_EOL;
