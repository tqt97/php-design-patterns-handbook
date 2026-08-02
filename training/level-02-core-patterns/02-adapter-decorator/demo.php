<?php

declare(strict_types=1);

interface AdaptervDecoratorDemo
{
    public function run(string $input): string;
}

final class DefaultAdaptervDecoratorDemo implements AdaptervDecoratorDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '02-adapter-decorator:' . $input . ':ok';
    }
}

$result = (new DefaultAdaptervDecoratorDemo())->run('training');
assert($result === '02-adapter-decorator:training:ok');
echo $result, PHP_EOL;
