<?php

declare(strict_types=1);

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

interface InvoicePort
{
    public function fetch(string $id): array;
}
final class LegacyInvoiceSdk
{
    public function getRecord(string $key): object
    {
        return (object) ['key' => $key, 'status' => 'OK'];
    }
}
final class LegacyInvoiceAdapter implements InvoicePort
{
    public function __construct(private LegacyInvoiceSdk $sdk)
    {
    }
    public function fetch(string $id): array
    {
        $r = $this->sdk->getRecord($id);
        return ['id' => $r->key, 'active' => $r->status === 'OK'];
    }
}
$record = (new LegacyInvoiceAdapter(new LegacyInvoiceSdk()))->fetch('INV-2026-001');
expect($record['id'] === 'INV-2026-001', 'mapped id');
expect($record['active'] === true, 'mapped status');
echo 'PASS kata 111' . PHP_EOL;
