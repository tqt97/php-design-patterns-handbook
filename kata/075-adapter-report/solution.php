<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ReportPort
{
    public function fetch(string $id): array;
}
final class LegacyReportSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyReportAdapter implements ReportPort
{
    public function __construct(private LegacyReportSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyReportAdapter(new LegacyReportSdk()))->fetch('sales-monthly');
expect($record['id'] === 'sales-monthly', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 75' . PHP_EOL;
