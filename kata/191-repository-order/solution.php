<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class OrderRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface OrderRepository
{
    public function save(OrderRecord $record): void;
    public function byId(string $id): ?OrderRecord;
}
final class InMemoryOrderRepository implements OrderRepository
{
    private array $items = [];
    public function save(OrderRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?OrderRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryOrderRepository();
$repo->save(new OrderRecord('ORD-1001', 'active'));
expect($repo->byId('ORD-1001')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 191' . PHP_EOL;
