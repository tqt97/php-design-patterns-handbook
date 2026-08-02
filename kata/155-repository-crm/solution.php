<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CrmRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface CrmRepository
{
    public function save(CrmRecord $record): void;
    public function byId(string $id): ?CrmRecord;
}
final class InMemoryCrmRepository implements CrmRepository
{
    private array $items = [];
    public function save(CrmRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?CrmRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryCrmRepository();
$repo->save(new CrmRecord('lead-202', 'active'));
expect($repo->byId('lead-202')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 155' . PHP_EOL;
