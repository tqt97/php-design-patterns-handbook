<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class CsvRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface CsvRepository
{
    public function save(CsvRecord $record): void;
    public function byId(string $id): ?CsvRecord;
}
final class InMemoryCsvRepository implements CsvRepository
{
    private array $items = [];
    public function save(CsvRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?CsvRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryCsvRepository();
$repo->save(new CsvRecord('customers.csv', 'active'));
expect($repo->byId('customers.csv')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 83' . PHP_EOL;
