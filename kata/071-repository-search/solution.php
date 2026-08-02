<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class SearchRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface SearchRepository
{
    public function save(SearchRecord $record): void;
    public function byId(string $id): ?SearchRecord;
}
final class InMemorySearchRepository implements SearchRepository
{
    private array $items = [];
    public function save(SearchRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?SearchRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemorySearchRepository();
$repo->save(new SearchRecord('php-pattern', 'active'));
expect($repo->byId('php-pattern')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 71' . PHP_EOL;
