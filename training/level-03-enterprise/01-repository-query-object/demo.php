<?php

declare(strict_types=1);

interface RepositoryvQueryObjectDemo
{
    public function run(string $input): string;
}

final class DefaultRepositoryvQueryObjectDemo implements RepositoryvQueryObjectDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '01-repository-query-object:' . $input . ':ok';
    }
}

$result = (new DefaultRepositoryvQueryObjectDemo())->run('training');
assert($result === '01-repository-query-object:training:ok');
echo $result, PHP_EOL;
