<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class SupportRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface SupportRepository
{
    public function save(SupportRecord $record): void;
    public function byId(string $id): ?SupportRecord;
}
final class InMemorySupportRepository implements SupportRepository
{
    private array $items = [];
    public function save(SupportRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?SupportRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemorySupportRepository();
$repo->save(new SupportRecord('TICKET-88', 'active'));
expect($repo->byId('TICKET-88')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 203' . PHP_EOL;
