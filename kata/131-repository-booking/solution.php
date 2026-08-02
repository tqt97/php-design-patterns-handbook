<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class BookingRecord
{
    public function __construct(public string $id, public string $status)
    {
    }
}
interface BookingRepository
{
    public function save(BookingRecord $record): void;
    public function byId(string $id): ?BookingRecord;
}
final class InMemoryBookingRepository implements BookingRepository
{
    private array $items = [];
    public function save(BookingRecord $r): void
    {
        $this->items[$r->id] = $r;
    }
    public function byId(string $id): ?BookingRecord
    {
        return $this->items[$id] ?? null;
    }
}
$repo = new InMemoryBookingRepository();
$repo->save(new BookingRecord('room-12', 'active'));
expect($repo->byId('room-12')?->status === 'active', 'repository round trip');
expect($repo->byId('missing') === null, 'missing');
echo 'PASS kata 131' . PHP_EOL;
