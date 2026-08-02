<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class DiscountRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface DiscountRepository
{
    public function save(DiscountRecord $record): void;
    public function byId(string $id): ?DiscountRecord;
}
final class InMemoryDiscountRepository implements DiscountRepository
{
    private array $items = [];
    public function save(DiscountRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?DiscountRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryDiscountRepository();
$repo->save(new DiscountRecord('VIP20', 'active'));
expect($repo->byId('VIP20')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 119' . PHP_EOL;
