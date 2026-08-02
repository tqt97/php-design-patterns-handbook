<?php

declare(strict_types=1);

interface DesignReviewDemo
{
    public function run(string $input): string;
}

final class DefaultDesignReviewDemo implements DesignReviewDemo
{
    public function run(string $input): string
    {
        if ($input === '') {
            throw new InvalidArgumentException('Input required.');
        }
        return '01-design-review:' . $input . ':ok';
    }
}

$result = (new DefaultDesignReviewDemo())->run('training');
assert($result === '01-design-review:training:ok');
echo $result, PHP_EOL;
