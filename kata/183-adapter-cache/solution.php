<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CachePort
{
    public function fetch(string $id): array;
}
final class LegacyCacheSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyCacheAdapter implements CachePort
{
    public function __construct(private LegacyCacheSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyCacheAdapter(new LegacyCacheSdk()))->fetch('customer:42');
expect($record['id'] === 'customer:42', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 183' . PHP_EOL;
