<?php

declare(strict_types=1);

interface SpecificationvPolicyDemo
{
    public function run(string $input): string;
}

final class DefaultSpecificationvPolicyDemo implements SpecificationvPolicyDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '03-specification-policy:' . $input . ':ok';
    }
}

$result = (new DefaultSpecificationvPolicyDemo())->run('training');
assert($result === '03-specification-policy:training:ok');
echo $result, PHP_EOL;
