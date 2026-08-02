<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ReportConnector
{
    public function connect(): string;
}
final class PrimaryReportConnector implements ReportConnector
{
    public function connect(): string
    {
        return 'primary:report';
    }
}
final class BackupReportConnector implements ReportConnector
{
    public function connect(): string
    {
        return 'backup:report';
    }
}
abstract class ReportConnectorFactory
{
    abstract protected function create(): ReportConnector;
    public function open(): string
    {
        return $this->create()->connect();
    }
}
final class PrimaryReportFactory extends ReportConnectorFactory
{
    protected function create(): ReportConnector
    {
        return new PrimaryReportConnector();
    }
}
final class BackupReportFactory extends ReportConnectorFactory
{
    protected function create(): ReportConnector
    {
        return new BackupReportConnector();
    }
}
expect((new PrimaryReportFactory())->open() === 'primary:report', 'primary factory');
expect((new BackupReportFactory())->open() === 'backup:report', 'backup factory');
echo 'PASS kata 194' . PHP_EOL;
