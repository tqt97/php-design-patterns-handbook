<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface AuditPort
{
    public function fetch(string $id): array;
}
final class LegacyAuditSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyAuditAdapter implements AuditPort
{
    public function __construct(private LegacyAuditSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyAuditAdapter(new LegacyAuditSdk()))->fetch('user.updated');
expect($record['id'] === 'user.updated', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 159' . PHP_EOL;
