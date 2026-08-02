<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CsvOperation
{
    public function run(string $input): string;
}
final class CoreCsvOperation implements CsvOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardCsvDecorator implements CsvOperation
{
    public function __construct(private CsvOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedCsvDecorator implements CsvOperation
{
    public function __construct(private CsvOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'csv:' . $this->next->run($input);
    }
}
$op = new TaggedCsvDecorator(new TrimGuardCsvDecorator(new CoreCsvOperation()));
expect($op->run(' DEMO ') === 'csv:demo', 'decorator order');
echo 'PASS kata 100' . PHP_EOL;
