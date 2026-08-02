<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface BookingPort
{
    public function fetch(string $id): array;
}
final class LegacyBookingSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyBookingAdapter implements BookingPort
{
    public function __construct(private LegacyBookingSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyBookingAdapter(new LegacyBookingSdk()))->fetch('room-12');
expect($record['id'] === 'room-12', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 63' . PHP_EOL;
