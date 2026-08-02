<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface UserPort
{
    public function fetch(string $id): array;
}
final class LegacyUserSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyUserAdapter implements UserPort
{
    public function __construct(private LegacyUserSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyUserAdapter(new LegacyUserSdk()))->fetch('user-42');
expect($record['id'] === 'user-42', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 99' . PHP_EOL;
