<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InventoryPort
{
    public function fetch(string $id): array;
}
final class LegacyInventorySdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyInventoryAdapter implements InventoryPort
{
    public function __construct(private LegacyInventorySdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyInventoryAdapter(new LegacyInventorySdk()))->fetch('SKU-PHP-01');
expect($record['id'] === 'SKU-PHP-01', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 39' . PHP_EOL;
