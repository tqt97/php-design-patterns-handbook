<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface CheckoutPort
{
    public function fetch(string $id): array;
}
final class LegacyCheckoutSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyCheckoutAdapter implements CheckoutPort
{
    public function __construct(private LegacyCheckoutSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyCheckoutAdapter(new LegacyCheckoutSdk()))->fetch('checkout-101');
expect($record['id'] === 'checkout-101', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 171' . PHP_EOL;
