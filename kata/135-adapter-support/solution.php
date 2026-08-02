<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface SupportPort
{
    public function fetch(string $id): array;
}
final class LegacySupportSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacySupportAdapter implements SupportPort
{
    public function __construct(private LegacySupportSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacySupportAdapter(new LegacySupportSdk()))->fetch('TICKET-88');
expect($record['id'] === 'TICKET-88', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 135' . PHP_EOL;
