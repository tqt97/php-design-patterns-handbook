<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CrmPort
{
    public function fetch(string $id): array;
}
final class LegacyCrmSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyCrmAdapter implements CrmPort
{
    public function __construct(private LegacyCrmSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyCrmAdapter(new LegacyCrmSdk()))->fetch('lead-202');
expect($record['id'] === 'lead-202', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 87' . PHP_EOL;
