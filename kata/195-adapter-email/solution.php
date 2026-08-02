<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface EmailPort
{
    public function fetch(string $id): array;
}
final class LegacyEmailSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyEmailAdapter implements EmailPort
{
    public function __construct(private LegacyEmailSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyEmailAdapter(new LegacyEmailSdk()))->fetch('welcome@example.com');
expect($record['id'] === 'welcome@example.com', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 195' . PHP_EOL;
