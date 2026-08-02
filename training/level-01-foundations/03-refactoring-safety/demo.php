<?php

declare(strict_types=1);

interface SafetykhirefactorDemo
{
    public function run(string $input): string;
}

final class DefaultSafetykhirefactorDemo implements SafetykhirefactorDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '03-refactoring-safety:' . $input . ':ok';
    }
}

$result = (new DefaultSafetykhirefactorDemo())->run('training');
assert($result === '03-refactoring-safety:training:ok');
echo $result, PHP_EOL;
