<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class ReportRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface ReportRepository
{
    public function save(ReportRecord $record): void;
    public function byId(string $id): ?ReportRecord;
}
final class InMemoryReportRepository implements ReportRepository
{
    private array $items = [];
    public function save(ReportRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?ReportRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryReportRepository();
$repo->save(new ReportRecord('sales-monthly', 'active'));
expect($repo->byId('sales-monthly')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 143' . PHP_EOL;
