<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SearchPort
{
    public function fetch(string $id): array;
}
final class LegacySearchSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacySearchAdapter implements SearchPort
{
    public function __construct(private LegacySearchSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacySearchAdapter(new LegacySearchSdk()))->fetch('php-pattern');
expect($record['id'] === 'php-pattern', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 3' . PHP_EOL;
