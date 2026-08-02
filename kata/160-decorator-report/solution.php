<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ReportOperation
{
    public function run(string $input): string;
}
final class CoreReportOperation implements ReportOperation
{
    public function run(string $input): string
    {
        return strtolower(trim($input));
    }
}
final class TrimGuardReportDecorator implements ReportOperation
{
    public function __construct(private ReportOperation $next)
    {
    }
    public function run(string $input): string
    {
        if (trim($input) === '')
            throw new InvalidArgumentException('empty');
        return $this->next->run($input);
    }
}
final class TaggedReportDecorator implements ReportOperation
{
    public function __construct(private ReportOperation $next)
    {
    }
    public function run(string $input): string
    {
        return 'report:' . $this->next->run($input);
    }
}
$op = new TaggedReportDecorator(new TrimGuardReportDecorator(new CoreReportOperation()));
expect($op->run(' DEMO ') === 'report:demo', 'decorator order');
echo 'PASS kata 160' . PHP_EOL;
