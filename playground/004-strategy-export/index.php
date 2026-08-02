<?php

declare(strict_types=1);

final class P4Context
{
    public function __construct(private string $pattern, private string $domain)
    {
    }

    public function run(): string
    {
        return sprintf('[%s] xử lý tình huống %s thành công', strtoupper($this->pattern), $this->domain);
    }
}

$result = (new P4Context('strategy', 'export'))->run();
assert(str_contains($result, strtoupper('strategy')));
echo $result . PHP_EOL;
