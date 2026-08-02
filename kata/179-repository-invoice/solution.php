<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class InvoiceRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface InvoiceRepository
{
    public function save(InvoiceRecord $record): void;
    public function byId(string $id): ?InvoiceRecord;
}
final class InMemoryInvoiceRepository implements InvoiceRepository
{
    private array $items = [];
    public function save(InvoiceRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?InvoiceRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryInvoiceRepository();
$repo->save(new InvoiceRecord('INV-2026-001', 'active'));
expect($repo->byId('INV-2026-001')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 179' . PHP_EOL;
