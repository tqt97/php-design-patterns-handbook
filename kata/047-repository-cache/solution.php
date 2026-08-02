<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CacheRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface CacheRepository
{
    public function save(CacheRecord $record): void;
    public function byId(string $id): ?CacheRecord;
}
final class InMemoryCacheRepository implements CacheRepository
{
    private array $items = [];
    public function save(CacheRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?CacheRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryCacheRepository();
$repo->save(new CacheRecord('customer:42', 'active'));
expect($repo->byId('customer:42')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 47' . PHP_EOL;
