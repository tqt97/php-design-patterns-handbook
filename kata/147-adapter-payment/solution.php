<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface PaymentPort
{
    public function fetch(string $id): array;
}
final class LegacyPaymentSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyPaymentAdapter implements PaymentPort
{
    public function __construct(private LegacyPaymentSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyPaymentAdapter(new LegacyPaymentSdk()))->fetch('pay_1001');
expect($record['id'] === 'pay_1001', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 147' . PHP_EOL;
