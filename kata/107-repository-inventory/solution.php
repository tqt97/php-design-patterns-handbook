<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class InventoryRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface InventoryRepository
{
    public function save(InventoryRecord $record): void;
    public function byId(string $id): ?InventoryRecord;
}
final class InMemoryInventoryRepository implements InventoryRepository
{
    private array $items = [];
    public function save(InventoryRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?InventoryRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryInventoryRepository();
$repo->save(new InventoryRecord('SKU-PHP-01', 'active'));
expect($repo->byId('SKU-PHP-01')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 107' . PHP_EOL;
