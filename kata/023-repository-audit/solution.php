<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class AuditRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface AuditRepository
{
    public function save(AuditRecord $record): void;
    public function byId(string $id): ?AuditRecord;
}
final class InMemoryAuditRepository implements AuditRepository
{
    private array $items = [];
    public function save(AuditRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?AuditRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryAuditRepository();
$repo->save(new AuditRecord('user.updated', 'active'));
expect($repo->byId('user.updated')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 23' . PHP_EOL;
