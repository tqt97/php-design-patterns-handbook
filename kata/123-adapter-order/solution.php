<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface OrderPort
{
    public function fetch(string $id): array;
}
final class LegacyOrderSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyOrderAdapter implements OrderPort
{
    public function __construct(private LegacyOrderSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyOrderAdapter(new LegacyOrderSdk()))->fetch('ORD-1001');
expect($record['id'] === 'ORD-1001', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 123' . PHP_EOL;
