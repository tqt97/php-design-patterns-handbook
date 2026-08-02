<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class ShippingRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface ShippingRepository
{
    public function save(ShippingRecord $record): void;
    public function byId(string $id): ?ShippingRecord;
}
final class InMemoryShippingRepository implements ShippingRepository
{
    private array $items = [];
    public function save(ShippingRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?ShippingRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryShippingRepository();
$repo->save(new ShippingRecord('HCM-HN', 'active'));
expect($repo->byId('HCM-HN')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 95' . PHP_EOL;
