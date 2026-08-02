<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface ShippingPort
{
    public function fetch(string $id): array;
}
final class LegacyShippingSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyShippingAdapter implements ShippingPort
{
    public function __construct(private LegacyShippingSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyShippingAdapter(new LegacyShippingSdk()))->fetch('HCM-HN');
expect($record['id'] === 'HCM-HN', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 27' . PHP_EOL;
