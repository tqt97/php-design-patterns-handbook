<?php

declare(strict_types=1);

final class P33Context
{
    public function __construct(private string $pattern, private string $domain)
    {
    }

    public function run(): string
    {
        return sprintf('[%s] xử lý tình huống %s thành công', strtoupper($this->pattern), $this->domain);
    }
}

$result = (new P33Context('observer', 'pricing'))->run();
assert(str_contains($result, strtoupper('observer')));
echo $result . PHP_EOL;
