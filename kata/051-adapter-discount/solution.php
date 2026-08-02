<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface DiscountPort
{
    public function fetch(string $id): array;
}
final class LegacyDiscountSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyDiscountAdapter implements DiscountPort
{
    public function __construct(private LegacyDiscountSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyDiscountAdapter(new LegacyDiscountSdk()))->fetch('VIP20');
expect($record['id'] === 'VIP20', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 51' . PHP_EOL;
