<?php

declare(strict_types=1);

interface CleanHexagonalArchitectureDemo
{
    public function run(string $input): string;
}

final class DefaultCleanHexagonalArchitectureDemo implements CleanHexagonalArchitectureDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '01-clean-hexagonal:' . $input . ':ok';
    }
}

$result = (new DefaultCleanHexagonalArchitectureDemo())->run('training');
assert($result === '01-clean-hexagonal:training:ok');
echo $result, PHP_EOL;
